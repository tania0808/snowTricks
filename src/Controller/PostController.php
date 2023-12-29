<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Trick;
use App\Form\TrickFormType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, TrickRepository $trickRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $trickRepository->getTrickPaginator($offset);

        return $this->render('main/index.html.twig', [
            'tricks' => $paginator,
            'previous' => $offset - TrickRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + TrickRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/tricks/{id}', name: 'tricks')]
    public function show(Trick $trick, TrickRepository $trickRepository): Response
    {
        $trick = $trickRepository->find($trick);

        return $this->render('posts/single_post.html.twig', [
            'trick' => $trick,
        ]);
    }

    #[Route('/trick/new', name: 'trick_new')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TrickFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $trick = new Trick();
            $trick->setName($data['name']);
            $trick->setDescription($data['description']);
            $trick->setAuthor($this->getUser());
            $trick->setCategory($data['category']);

            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Trick created!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('posts/new_post.html.twig', [
            'trickForm' => $form->createView(),
        ]);
    }
}
