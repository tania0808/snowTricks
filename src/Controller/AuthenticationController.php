<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private JWTTokenManagerInterface $JWTTokenManager;

    public function __construct(EmailVerifier $emailVerifier, JWTTokenManagerInterface $JWTTokenManager)
    {
        $this->emailVerifier = $emailVerifier;
        $this->JWTTokenManager = $JWTTokenManager;
    }
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('authentication/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();

                // Sending the confirmation email
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('tania08082000@gmail.com', 'AcmeMailBot'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('authentication/confirmation_email.html.twig'),
                    $this->JWTTokenManager
                );

                $this->addFlash('success', 'Your account has been created. Please check your email for a verification link.');

                return $this->redirectToRoute('app_login');
            }
            catch (UniqueConstraintViolationException $e) {
                // Handle unique constraint violation (username or email already exists)
                $form->get('username')->addError(new FormError('Username already taken'));
                $form->get('email')->addError(new FormError('Email already registered'));
            }

        }

        return $this->render('authentication/signup.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        throw new \Exception('logout should never be reached!');
    }

    /**
     * @throws JWTDecodeFailureException
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository, JWTEncoderInterface $jwtEncoder): Response
    {
        try {
            $token = $request->query->get('token');
            $data = $jwtEncoder->decode($token);

            $id = $data['id'];

            if (null === $id) {
                return $this->redirectToRoute('app_register');
            }

            $user = $userRepository->find($id);

            if (null === $user) {
                return $this->redirectToRoute('app_register');
            }

            if($user->getIsVerified()) {
                $this->addFlash('success', 'Your email address has already been verified.');
                return $this->redirectToRoute('app_login');
            }

            $this->emailVerifier->handleEmailConfirmation($request, $user);
            $this->addFlash('success', 'Your email address has been verified.');

        } catch (JWTDecodeFailureException $e) {
            $this->addFlash('error', 'Invalid or expired token.');
            return $this->redirectToRoute('app_register');
        }


        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}
