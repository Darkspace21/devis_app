<?php

namespace App\Controller;

use App\Entity\TypePrestation;
use App\Form\TypePrestationType;
use App\Repository\TypePrestationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/type/prestation')]
class TypePrestationController extends AbstractController
{
    #[Route('/', name: 'type_prestation_index', methods: ['GET'])]
    public function index(TypePrestationRepository $typePrestationRepository): Response
    {
        return $this->render('type_prestation/index.html.twig', [
            'type_prestations' => $typePrestationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'type_prestation_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $typePrestation = new TypePrestation();
        $form = $this->createForm(TypePrestationType::class, $typePrestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($typePrestation);
            $entityManager->flush();

            return $this->redirectToRoute('type_prestation_index');
        }

        return $this->render('type_prestation/new.html.twig', [
            'type_prestation' => $typePrestation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'type_prestation_show', methods: ['GET'])]
    public function show(TypePrestation $typePrestation): Response
    {
        return $this->render('type_prestation/show.html.twig', [
            'type_prestation' => $typePrestation,
        ]);
    }

    #[Route('/{id}/edit', name: 'type_prestation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypePrestation $typePrestation): Response
    {
        $form = $this->createForm(TypePrestationType::class, $typePrestation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('type_prestation_index');
        }

        return $this->render('type_prestation/edit.html.twig', [
            'type_prestation' => $typePrestation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'type_prestation_delete', methods: ['DELETE'])]
    public function delete(Request $request, TypePrestation $typePrestation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typePrestation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($typePrestation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('type_prestation_index');
    }
}
