<?php

namespace App\Controller;

use App\Entity\PiecesNecessaire;
use App\Form\PiecesNecessaireType;
use App\Repository\PiecesNecessaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pieces/necessaire')]
class PiecesNecessaireController extends AbstractController
{
    #[Route('/', name: 'pieces_necessaire_index', methods: ['GET'])]
    public function index(PiecesNecessaireRepository $piecesNecessaireRepository): Response
    {
        return $this->render('pieces_necessaire/index.html.twig', [
            'pieces_necessaires' => $piecesNecessaireRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'pieces_necessaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $piecesNecessaire = new PiecesNecessaire();
        $form = $this->createForm(PiecesNecessaireType::class, $piecesNecessaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($piecesNecessaire);
            $entityManager->flush();

            return $this->redirectToRoute('pieces_necessaire_index');
        }

        return $this->render('pieces_necessaire/new.html.twig', [
            'pieces_necessaire' => $piecesNecessaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'pieces_necessaire_show', methods: ['GET'])]
    public function show(PiecesNecessaire $piecesNecessaire): Response
    {
        return $this->render('pieces_necessaire/show.html.twig', [
            'pieces_necessaire' => $piecesNecessaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'pieces_necessaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PiecesNecessaire $piecesNecessaire): Response
    {
        $form = $this->createForm(PiecesNecessaireType::class, $piecesNecessaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pieces_necessaire_index');
        }

        return $this->render('pieces_necessaire/edit.html.twig', [
            'pieces_necessaire' => $piecesNecessaire,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'pieces_necessaire_delete', methods: ['DELETE'])]
    public function delete(Request $request, PiecesNecessaire $piecesNecessaire): Response
    {
        if ($this->isCsrfTokenValid('delete'.$piecesNecessaire->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($piecesNecessaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pieces_necessaire_index');
    }
}
