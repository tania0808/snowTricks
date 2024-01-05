<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/{trick}/comment/add', name: 'app_comment_add', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, Trick $trick): Response
    {
        $form = $this->createForm(CommentFormType::class, null, [
            'trickId' => $trick->getId(),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setAuthor($this->getUser());

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commented !');
            return $this->redirectToRoute('app_home');
        }

        return $this->redirectToRoute('app_home');
    }


}
