<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\User;
use App\Form\TrickFormType;
use App\Form\UserProfileType;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

\Locale::setDefault('en');

class UserController extends AbstractController
{
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
    public function edit(Request $request, User $user, UserRepository $userRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $userRepository->find($user);
        $form = $this->createForm(UserProfileType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            /** @var UploadedFile $profileImageFile */
            $profileImageFile = $form->get('imageName')->getData();

            if($profileImageFile) {
                $originalFileName = pathinfo(
                    $profileImageFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );

                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $profileImageFile->guessExtension();

                try {
                    $profileImageFile->move(
                        $this->getParameter('profile_image_directory'),
                        $newFileName
                    );
                    if($user->getImageName() !== null) {
                        unlink($this->getParameter('profile_image_directory') . '/' . $user->getImageName());
                    }
                } catch (FileException $e) {
                    $this->addFlash('error', 'An error occurred during file upload');
                     return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
                }

                $user->setImageName($newFileName);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Profile information modified!');
            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }


        return $this->render('profile/edit_profile.html.twig', [
            'userForm' => $form->createView(),
        ]);
    }

}
