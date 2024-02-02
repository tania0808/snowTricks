<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserProfileType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Countries;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

\Locale::setDefault('en');

class UserController extends AbstractController
{
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/profile/{id}', name: 'user_profile')]
    public function show(User $user, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($user);
        $country = Countries::getName($user->getLocation() ?? 'FR');

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'country' => $country,
        ]);
    }

    #[Route('/profile/{id}/edit', name: 'user_edit')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, User $user, UserRepository $userRepository, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        if ($user !== $this->getUser()) {
            throw new AccessDeniedException('This action is unauthorized!', 403);
        }

        $user = $userRepository->find($user);
        $form = $this->createForm(UserProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            /** @var UploadedFile $profileImageFile */
            $profileImageFile = $form->get('imageName')->getData();

            if ($profileImageFile) {
                $newFileName = $fileUploader->upload($profileImageFile);

                if (null !== $user->getImageName()) {
                    $fileUploader->delete($user->getImageName());
                }

                $user->setImageName($newFileName);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profile information modified!');

            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }

        return $this->render('profile/_profile-form.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

    #[Route('/profile/{id}/edit/password', name: 'user_edit_password')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function editPassword(Request $request, User $user, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if ($user !== $this->getUser()) {
            throw new AccessDeniedException('This action is unauthorized!', 403);
        }

        $user = $userRepository->find($user);

        $passwordForm = $this->createForm(UserPasswordType::class);

        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword(
                $user,
                $passwordForm->get('newPassword')->getData()
            ));
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profile information modified!');

            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }

        return $this->render('profile/_password-form.html.twig', [
            'passwordForm' => $passwordForm->createView(),
        ]);
    }
}
