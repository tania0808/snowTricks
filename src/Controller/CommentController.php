<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommentController extends AbstractController
{
    #[Route('/{trick}/comment/add', name: 'app_comment_add', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(Request $request, EntityManagerInterface $entityManager, Trick $trick): Response
    {
        $form = $this->createForm(CommentFormType::class, null, [
            'trickId' => $trick->getId(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setAuthor($this->getUser());
            $comment->setTrick($trick);

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commented !');

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('tricks/single_trick.html.twig', [
            'trick' => $trick,
            'media' => $trick->getMedia(),
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/{trick}/comment/delete/{id}', name: 'comment_delete')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Comment $comment, EntityManagerInterface $entityManager, Trick $trick): Response
    {
        if ($comment->getAuthor() !== $this->getUser()) {
            throw new AccessDeniedException('This action is unauthorized!', 403);
        }

        $entityManager->remove($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Comment deleted!');

        return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
    }
}
