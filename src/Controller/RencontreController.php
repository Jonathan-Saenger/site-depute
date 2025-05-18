<?php

namespace App\Controller;

use App\Repository\RencontreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RencontreController extends AbstractController
{
    #[Route('/rencontre', name: 'app_rencontre')]
    public function index(RencontreRepository $rencontreRepository): Response
    {
        return $this->render('pages/rencontre/rencontre.html.twig', [
            'rencontres' => $rencontreRepository->findUpcomingRencontre(),
        ]);
    }
}
