<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    private array $messages = [
        'Hello', 'Hi', 'Bye'
    ];
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        //$this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('main/index.html.twig', []);
    }

    #[Route('/messages/{id}', name: 'show_one')]
    public function showOne(int $id): Response
    {
        return new Response($this->messages[$id]);
    }
}
