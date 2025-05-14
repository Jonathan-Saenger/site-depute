<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RencontreController extends AbstractController
{
    #[Route('/rencontre', name: 'app_rencontre')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('pages/rencontre.html.twig', [
            'controller_name' => 'RencontreController',
            'articles' => $articleRepository->findPublishedArticles(),
        ]);
    }
}
