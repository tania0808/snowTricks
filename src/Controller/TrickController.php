<?php

namespace App\Controller;

use App\Entity\Media;
use App\Entity\Trick;
use App\Form\CommentFormType;
use App\Form\EditTrickFormType;
use App\Form\TrickFormType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TrickController extends AbstractController
{
    #[Route('/comments-partial/{offset}/{trick}', name: 'comments_partial')]
    public function commentsPartial(CommentRepository $commentRepository, TrickRepository $trickRepository, int $offset, Trick $trick): Response
    {
        $trick = $trickRepository->find($trick);
        $paginator = $commentRepository->getCommentPaginator($offset, $trick);

        return $this->render('comment/_comments_partial.html.twig', [
            'trick' => $trick,
            'comments' => $paginator,
        ]);
    }

    #[Route('/tricks-partial/{offset}', name: 'tricks_partial')]
    public function tricksPartial(TrickRepository $trickRepository, int $offset): Response
    {
        $paginator = $trickRepository->getTrickPaginator($offset);

        return $this->render('tricks/partials/_tricks_partial.html.twig', [
            'tricks' => $paginator,
        ]);
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request, TrickRepository $trickRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $trickRepository->getTrickPaginator($offset);

        return $this->render('main/index.html.twig', [
            'tricks' => $paginator,
            'previous' => $offset - TrickRepository::PAGINATOR_PER_PAGE,
            'next' => $offset + TrickRepository::PAGINATOR_PER_PAGE,
        ]);
    }

    #[Route('/tricks/{slug}', name: 'trick_show')]
    public function show(Request $request, Trick $trick, TrickRepository $trickRepository, CommentRepository $commentRepository): Response
    {
        $trick = $trickRepository->find($trick);
        $media = $trick->getMedia();
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($offset, $trick);

        $form = $this->createForm(CommentFormType::class, null, [
            'trickId' => $trick->getId(),
        ]);

        return $this->render('tricks/single_trick.html.twig', [
            'trick' => $trick,
            'media' => $media,
            'comments' => $paginator,
            'commentForm' => $form->createView(),
            'next' => $offset + $commentRepository::PAGINATOR_PER_PAGE,
        ]);
    }

    #[Route('/trick/new', name: 'trick_new')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(TrickFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            $trick->setAuthor($this->getUser());
            $trick->setSlug((new AsciiSlugger())->slug(strtolower($trick->getName())));

            $this->uploadImages($form, $fileUploader, $trick);

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
        $this->guardAgainstUnauthorizedUser($trick);

        $entityManager->remove($trick);
        $entityManager->flush();

        $this->addFlash('success', 'Trick deleted!');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/trick/edit/{id}', name: 'trick_edit')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Trick $trick, Request $request, EntityManagerInterface $entityManager, TrickRepository $trickRepository, FileUploader $fileUploader): Response
    {
        $this->guardAgainstUnauthorizedUser($trick);

        $trick = $trickRepository->find($trick);
        $form = $this->createForm(EditTrickFormType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->uploadVideos($form, $trick);
            $trick = $form->getData();
            $this->uploadImages($form, $fileUploader, $trick);

            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Trick information modified!');

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('tricks/edit-trick-form.html.twig', [
            'trickForm' => $form->createView(),
            'trick' => $trick,
        ]);
    }

    #[Route('/media/edit/{id}', name: 'trick_media_edit')]
    public function edit_media(Trick $trick)
    {
        $this->guardAgainstUnauthorizedUser($trick);

        return $this->render('tricks/edit_media.html.twig', [
            'trick' => $trick,
        ]);
    }

    #[Route('/media/delete/{id}', name: 'trick_media_delete')]
    public function delete_media(Media $media, EntityManagerInterface $entityManager, FileUploader $fileUploader)
    {
        $this->guardAgainstUnauthorizedUser($media->getTrick());

        if ('image' === $media->getType()) {
            $fileUploader->delete($media->getName());
        }

        $entityManager->remove($media);
        $entityManager->flush();

        $this->addFlash('success', 'Media deleted!');

        return $this->redirectToRoute('trick_edit', ['id' => $media->getTrick()->getId()]);
    }

    private function uploadVideos($form, $trick)
    {
        $videos = $form->get('videos')->getData();

        if ($videos) {
            foreach ($videos as $video) {
                $media = new Media();
                $media->setName($video->getName());
                $media->setType('video');
                $trick->addMedium($media);
            }
        }
    }

    private function uploadImages($form, $fileUploader, $trick)
    {
        /** @var UploadedFile[] $profileImageFile */
        $images = $form->get('media')->getData();

        if ($images) {
            foreach ($images as $image) {
                $newFileName = $fileUploader->upload($image);
                $media = new Media();
                $media->setName($newFileName);
                $media->setType('image');
                $trick->addMedium($media);
            }
        }
    }

    private function guardAgainstUnauthorizedUser(Trick $trick): ?RedirectResponse
    {
        if ($trick->getAuthor() !== $this->getUser()) {
            throw new AccessDeniedException('This action is unauthorized!', 403);
        }

        return null;
    }
}
