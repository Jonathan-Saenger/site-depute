<?php

namespace App\Controller;

use App\Repository\RencontreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Enum\CommuneEnum;
use App\Enum\RencontreEnum;

final class RencontreController extends AbstractController
{
    #[Route('/rencontre', name: 'app_rencontre')]
    public function index(Request $request, RencontreRepository $rencontreRepository): Response
    {
        $commune = $request->query->get('commune');
        $type = $request->query->get('type');

        $communes = $rencontreRepository->findCommunes();
        $types = $rencontreRepository->findTypes();

        // Génère les labels pour les communes
        $communeLabels = [];
        foreach ($communes as $c) {
            $communeLabels[$c] = CommuneEnum::tryFrom($c)?->getLabel() ?? $c;
        }
        // Génère les labels pour les types
        $typeLabels = [];
        foreach ($types as $t) {
            $typeLabels[$t] = RencontreEnum::tryFrom($t)?->getLabel() ?? $t;
        }

        return $this->render('pages/rencontre/rencontre.html.twig', [
            'rencontres' => $rencontreRepository->findUpcomingRencontre($commune, $type),
            'communes' => $communes,
            'types' => $types,
            'communeLabels' => $communeLabels,
            'typeLabels' => $typeLabels,
            'selected_commune' => $commune,
            'selected_type' => $type,
        ]);
    }
}
