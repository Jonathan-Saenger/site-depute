<?php

namespace App\Controller;

use App\Entity\Rencontre;
use App\Form\RencontreForm;
use App\Repository\RencontreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
final class AdminRencontreController extends AbstractController
{
    #[Route('/rencontre', name: 'app_admin_rencontre_index', methods: ['GET'])]
    public function index(RencontreRepository $rencontreRepository): Response
    {
        return $this->render('admin_rencontre/index.html.twig', [
            'rencontres' => $rencontreRepository->findAllOrderedByDate(),
        ]);
    }

    #[Route('/rencontre/new', name: 'app_admin_rencontre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rencontre = new Rencontre();
        $form = $this->createForm(RencontreForm::class, $rencontre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rencontre);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_rencontre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_rencontre/new.html.twig', [
            'rencontre' => $rencontre,
            'form' => $form,
        ]);
    }

    #[Route('/rencontre/{id}', name: 'app_admin_rencontre_show', methods: ['GET'])]
    public function show(Rencontre $rencontre): Response
    {
        return $this->render('admin_rencontre/show.html.twig', [
            'rencontre' => $rencontre,
        ]);
    }

    #[Route('/rencontre/{id}/edit', name: 'app_admin_rencontre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rencontre $rencontre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RencontreForm::class, $rencontre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_rencontre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_rencontre/edit.html.twig', [
            'rencontre' => $rencontre,
            'form' => $form,
        ]);
    }

    #[Route('/rencontre/{id}', name: 'app_admin_rencontre_delete', methods: ['POST'])]
    public function delete(Request $request, Rencontre $rencontre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rencontre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rencontre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_rencontre_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/rencontre/{id}/toggle-visible', name: 'app_admin_rencontre_toggle_visible', methods: ['POST'])]
    public function toggleVisible(Request $request, RencontreRepository $rencontreRepository, int $id, EntityManagerInterface $entityManager): Response
    {
        $rencontre = $rencontreRepository->find($id);
        if (!$rencontre) {
            throw $this->createNotFoundException('Rencontre non trouvée');
        }

        if ($this->isCsrfTokenValid('toggle-visible'.$rencontre->getId(), $request->getPayload()->getString('_token'))) {
            $rencontre->setVisible(!$rencontre->isVisible());
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_rencontre_index');
    }
}
