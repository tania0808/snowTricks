<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    private array $messages = [
        'Hello', 'Hi', 'Bye'
    ];
    #[Route('/', name: 'app_home')]
    public function index(TrickRepository $trickRepository): Response
    {
        $trick = $trickRepository->findAll();
        dd($trick);
        return $this->render('main/index.html.twig', []);
    }
}
