<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class NewsController extends AbstractController
{
    public function __construct(private ArticleRepository $articleRepository)
    {
    }

    #[Route('/actualite', name: 'app_news_index')]
    public function index(): Response
    {
        return $this->render('partials/_news.html.twig', [
            'articles' => $this->articleRepository->findPublishedArticles(),
        ]);
    }

    #[Route('/actualite/{category}', name: 'app_news_category', requirements: ['category' => 'circonscription|assemblee'])]
    public function indexByCategory(string $category): Response
    {
        $template = match ($category) {
            'circonscription' => 'pages/actualites/circonscription.html.twig',
            'assemblee' => 'pages/actualites/assemblee.html.twig',
            default => throw $this->createNotFoundException('Category not found'),
        };

        return $this->render($template, [
            'articles' => $this->articleRepository->findPublishedArticlesByCategory(strtoupper($category)),
        ]);
    }

    #[Route('/actualite/article/{id}', name: 'app_news_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $article = $this->articleRepository->find($id);
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }
        return $this->render('pages/actualites/article.html.twig', [
            'article' => $article,
        ]);
    }
}