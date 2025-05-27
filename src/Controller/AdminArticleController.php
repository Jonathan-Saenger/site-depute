<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleCategoryForm;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
final class AdminArticleController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard', methods: ['GET'])]
    public function dashboard(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin_article/accueil_admin.html.twig', [
            'articlesPublished' => count($articleRepository->findBy(['isPublished' => true])),
            'articlesDrafts' => count($articleRepository->findBy(['isPublished' => false])),
            'upcomingEvents' => 0, // À implémenter avec le futur CRUD des rendez-vous
            'pastEvents' => 0,     // À implémenter avec le futur CRUD des rendez-vous
        ]);
    }

    #[Route('/article', name: 'app_admin_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin_article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/article/new', name: 'app_admin_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleCategoryForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }    #[Route('/article/{slug}', name: 'app_admin_article_show', requirements: ['slug' => '[a-z0-9\-]+'], methods: ['GET'])]
    public function show(ArticleRepository $articleRepository, string $slug): Response
    {
        $article = $articleRepository->findBySlug($slug);
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        return $this->render('admin_article/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/article/{slug}/edit', name: 'app_admin_article_edit', requirements: ['slug' => '[a-z0-9\-]+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, ArticleRepository $articleRepository, string $slug, EntityManagerInterface $entityManager): Response
    {
        $article = $articleRepository->findBySlug($slug);
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        $form = $this->createForm(ArticleCategoryForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/article/{slug}', name: 'app_admin_article_delete', requirements: ['slug' => '[a-z0-9\-]+'], methods: ['POST'])]
    public function delete(Request $request, ArticleRepository $articleRepository, string $slug, EntityManagerInterface $entityManager): Response
    {
        $article = $articleRepository->findBySlug($slug);
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        if ($this->isCsrfTokenValid('delete'.$article->getSlug(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_article_index', [], Response::HTTP_SEE_OTHER);
    }    #[Route('/article/{slug}/toggle-publish', name: 'app_admin_article_toggle_publish', requirements: ['slug' => '[a-z0-9\-]+'], methods: ['POST'])]
    public function togglePublish(Request $request, ArticleRepository $articleRepository, string $slug, EntityManagerInterface $entityManager): Response
    {
        $article = $articleRepository->findBySlug($slug);
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }

        if ($this->isCsrfTokenValid('toggle-publish'.$article->getSlug(), $request->getPayload()->getString('_token'))) {
            $article->setIsPublished(!$article->isPublished());
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_article_index');
    }
}
