<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    private array $messages = [
        'Hello', 'Hi', 'Bye'
    ];
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

    #[Route('/trick/{id}', name: 'trick')]
    public function show(Trick $trick, TrickRepository $trickRepository): Response
    {
        $trick = $trickRepository->find($trick);

        return $this->render('posts/single_post.html.twig', [
            'trick' => $trick,
        ]);
    }
}
