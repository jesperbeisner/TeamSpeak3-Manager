<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActiveController extends AbstractController
{
    #[Route('/active', name: 'active')]
    public function index(): Response
    {
        return $this->render('active/index.html.twig');
    }
}
