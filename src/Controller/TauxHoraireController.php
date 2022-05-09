<?php

namespace App\Controller;

use App\Entity\TauxHoraire;
use App\Form\TauxHoraireType;
use App\Repository\TauxHoraireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/taux/horaire')]
class TauxHoraireController extends AbstractController
{
    #[Route('/', name: 'taux_horaire_index', methods: ['GET'])]
    public function index(TauxHoraireRepository $tauxHoraireRepository): Response
    {
        return $this->render('taux_horaire/index.html.twig', [
            'taux_horaires' => $tauxHoraireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'taux_horaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tauxHoraire = new TauxHoraire();
        $form = $this->createForm(TauxHoraireType::class, $tauxHoraire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($tauxHoraire);
            $entityManager->flush();

            return $this->redirectToRoute('taux_horaire_index');
        }

        return $this->render('taux_horaire/new.html.twig', [
            'taux_horaire' => $tauxHoraire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'taux_horaire_show', methods: ['GET'])]
    public function show(TauxHoraire $tauxHoraire): Response
    {
        return $this->render('taux_horaire/show.html.twig', [
            'taux_horaire' => $tauxHoraire,
        ]);
    }

    #[Route('/{id}/edit', name: 'taux_horaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TauxHoraire $tauxHoraire): Response
    {
        $form = $this->createForm(TauxHoraireType::class, $tauxHoraire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('taux_horaire_index');
        }

        return $this->render('taux_horaire/edit.html.twig', [
            'taux_horaire' => $tauxHoraire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'taux_horaire_delete', methods: ['DELETE'])]
    public function delete(Request $request, TauxHoraire $tauxHoraire): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tauxHoraire->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tauxHoraire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('taux_horaire_index');
    }
}
