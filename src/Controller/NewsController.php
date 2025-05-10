<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NewsController extends AbstractController
{
    #[Route('/news', name: 'app_news')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('partials/_news.html.twig', [
            'articles' => $articleRepository->findPublishedArticles(),
        ]);
    }
}