<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ErrorController extends AbstractController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function showException(\Throwable $exception): Response
    {
        $statusCode = $exception->getCode() ?? 500;

        $message = '';
        $details = '';
        switch ($statusCode) {
            case 403:
                $message = 'Access denied.';
                $details = 'Sorry, you do not have permission to access this page.';
                break;
            case 404:
                $message = 'Page not found.';
                $details = 'Sorry, the page you are looking for could not be found.';
                break;
            case 500:
                $message = 'Internal server error.';
                $details = 'Sorry, something went wrong.';
                // no break
            default:
                $details = 'Sorry, something went wrong.Please wait a few minutes and try again.';
                $message = 'An error occurred.';
        }

        return new Response(
            $this->twig->render('bundles/TwigBundle/Exception/error.html.twig', ['exception' => $exception, 'status' => $statusCode, 'message' => $message, 'details' => $details]),
            $statusCode
        );
    }
}
