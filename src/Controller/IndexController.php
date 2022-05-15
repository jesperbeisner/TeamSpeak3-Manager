<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\KeyHistoryRepository;
use App\Repository\ServerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ServerRepository $serverRepository, KeyHistoryRepository $keyHistoryRepository): Response
    {
        return $this->render('index/index.html.twig', [
            'servers' => $serverRepository->findBy(['synchronized' => true]),
            'keys' => $keyHistoryRepository->findBy([], ['created' => 'DESC']),
        ]);
    }
}
