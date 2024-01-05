<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\CommentFormType;
use App\Form\TrickFormType;
use App\Repository\TrickRepository;
use App\Service\FileUploader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrickController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, TrickRepository $trickRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $trickRepository->getTrickPaginator($offset);

        return $this->render('main/comment_form.html.twig', [
            'tricks' => $paginator,
            'previous' => $offset - TrickRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + TrickRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/tricks/{id}', name: 'tricks')]
    public function show(Trick $trick, TrickRepository $trickRepository): Response
    {
        $trick = $trickRepository->find($trick);
        $media = $trick->getMedia();

        $form = $this->createForm(CommentFormType::class);

        return $this->render('tricks/single_trick.html.twig', [
            'trick' => $trick,
            'media' => $media,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/trick/new', name: 'trick_new')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(TrickFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            $trick->setAuthor($this->getUser());

            /** @var UploadedFile[] $profileImageFile */
            $images = $form->get('media')->getData();
            if($images) {
                foreach($images as $image) {
                    $newFileName = $fileUploader->upload($image);
                    $media = new Media();
                    $media->setName($newFileName);
                    $media->setType('image');
                    $trick->addMedium($media);
                }
            }

            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Trick created!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('tricks/new_trick.html.twig', [
            'trickForm' => $form->createView(),
        ]);
    }

    #[Route('/trick/delete/{id}', name: 'trick_delete')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Trick $trick, EntityManagerInterface $entityManager): Response
    {
        if($trick->getAuthor() !== $this->getUser()) {
            $this->addFlash('error', 'You cannot delete this trick!');
            return $this->redirectToRoute('app_home');
        }

        $entityManager->remove($trick);
        $entityManager->flush();

        $this->addFlash('success', 'Trick deleted!');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/trick/edit/{id}', name: 'trick_edit')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Trick $trick, Request $request, EntityManagerInterface $entityManager): Response
    {
        dd($trick);
    }
}
