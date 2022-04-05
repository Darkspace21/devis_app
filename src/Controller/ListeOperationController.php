<?php

namespace App\Controller;

use App\Entity\ListeOperation;
use App\Form\ListeOperationType;
use App\Repository\ListeOperationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/liste/operation')]
class ListeOperationController extends AbstractController
{
    #[Route('/', name: 'liste_operation_index', methods: ['GET'])]
    public function index(ListeOperationRepository $listeOperationRepository): Response
    {
        return $this->render('liste_operation/index.html.twig', [
            'liste_operations' => $listeOperationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'liste_operation_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $listeOperation = new ListeOperation();
        $form = $this->createForm(ListeOperationType::class, $listeOperation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($listeOperation);
            $entityManager->flush();

            return $this->redirectToRoute('liste_operation_index');
        }

        return $this->render('liste_operation/new.html.twig', [
            'liste_operation' => $listeOperation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'liste_operation_show', methods: ['GET'])]
    public function show(ListeOperation $listeOperation): Response
    {
        return $this->render('liste_operation/show.html.twig', [
            'liste_operation' => $listeOperation,
        ]);
    }

    #[Route('/{id}/edit', name: 'liste_operation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ListeOperation $listeOperation): Response
    {
        $form = $this->createForm(ListeOperationType::class, $listeOperation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('liste_operation_index');
        }

        return $this->render('liste_operation/edit.html.twig', [
            'liste_operation' => $listeOperation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'liste_operation_delete', methods: ['DELETE'])]
    public function delete(Request $request, ListeOperation $listeOperation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$listeOperation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($listeOperation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('liste_operation_index');
    }
}
