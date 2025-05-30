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
        };

        return $this->render($template, [
            'articles' => $this->articleRepository->findPublishedArticlesByCategory(strtoupper($category)),
        ]);
    }    #[Route('/actualite/article/{slug}', name: 'app_news_show', requirements: ['slug' => '[a-z0-9\-]+'])]
    public function show(string $slug): Response
    {
        $article = $this->articleRepository->findPublishedBySlug($slug);
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        return $this->render('pages/actualites/article.html.twig', [
            'article' => $article,
        ]);
    }
}