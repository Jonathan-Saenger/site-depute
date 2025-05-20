<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('pages/home.html.twig', [
            'articles' => $articleRepository->findPublishedArticles(),
        ]);
    }

    #[Route('/depute', name: 'app_depute')]
    public function depute(): Response
    {
        return $this->render('pages/depute.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('pages/contact.html.twig');
    }

    #[Route ('/presse', name: 'app_presse')]
    public function press (): Response
    {
        return $this->render('pages/presse.html.twig');
    }

    #[Route ('/mentions-legales', name: 'app_mentions_legales')]
    public function mentionsLegales (): Response
    {
        return $this->render('pages/mentions.html.twig');
    }
}
