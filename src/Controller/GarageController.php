<?php

namespace App\Controller;

use App\Entity\Garage;
use App\Form\GarageType;
use App\Repository\GarageRepository;
use App\Repository\TauxHoraireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

#[Route('/garage')]
class GarageController extends AbstractController
{
    #[Route('/', name: 'garage_index', methods: ['GET'])]
    public function index(GarageRepository $garageRepository): Response
    {
        $id_user=$this->getUser()->getId();
        $garage=$garageRepository->liste_garage_user($id_user);
        return $this->render('garage/index.html.twig', [
            'garages' => $garage,
        ]);
    }

    #[Route('/new', name: 'garage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GarageRepository $Garage, TauxHoraireRepository $TauxHoraire): Response
    {
        $garage = new Garage();
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('nom_garage', TextType::class)
            ->add('Emplacement', TextType::class)
            ->add('T1', TextType::class)
            ->add('T2', TextType::class)
            ->add('T3', TextType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // mettre à jour taux horaire puis récuperer l'id
            // mettre à jour garage. 
            $id_user=$this->getUser()->getId();
            $infoGarage=$form->getData();
            //dump($infoGarage);

            $nom_garage=$infoGarage['nom_garage'];
            $emplacement=$infoGarage['Emplacement'];

            $T1=$infoGarage['T1'];
            $T2=$infoGarage['T2'];
            $T3=$infoGarage['T3'];

            //créer le taux horaire
            //recuperer l'id max qui correspond aux valeurs choisis
            //$TauxHoraire->ajout_taux_horaire($T1,$T2,$T3);
            $taux_horaire_id=$TauxHoraire->maxId($T1,$T2,$T3);
            //dump($taux_horaire_id);
            //dump($nom_garage,$emplacement,$id_user);
            $Garage->ajout_garage($taux_horaire_id['id'],$nom_garage,$emplacement,$id_user );
            $message='votre garage a bien été ajouté';
            return $this->redirectToRoute('garage_index',['message'=>$message]);
        }

        return $this->render('garage/new.html.twig', [
            'garage' => $garage,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'garage_show', methods: ['GET'])]
    public function show(GarageRepository $garageRepository, $id): Response
    {
        $id_user=$this->getUser()->getId();
        $garage_id=$id;
        $garage=$garageRepository->info_garage_user($id_user,$garage_id);
        //dump($garage);
        return $this->render('garage/show.html.twig', [
            'garage' => $garage,
        ]);
    }

    #[Route('/{id}/edit', name: 'garage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GarageRepository $garageRepository, $id): Response
    {
        // recuperer les info du garage a partir de l'id
        $infoGarage=$garageRepository->info_garage($id);
        // dump($infoGarage);
        // dump($infoGarage['id']);
        $garage = new Garage();
        $defaultData = ['message' => 'Type your message here'];
        $form = $this->createFormBuilder($defaultData)
            ->add('nom_garage', TextType::class,['data'=>$infoGarage['nom_garage']])
            ->add('emplacement', TextType::class,['data'=>$infoGarage['emplacement']])
            ->add('T1', TextType::class,['data'=>$infoGarage['t1']])
            ->add('T2', TextType::class,['data'=>$infoGarage['t2']])
            ->add('T3', TextType::class,['data'=>$infoGarage['t3']])
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            //créer la fonction pour update le garage
            // faire un update pour le garage
            $garageRepository->update_garage($data['nom_garage'],$data['emplacement'],$infoGarage['id'] );
            // faire un update pour le taux_horaire_id
            $garageRepository->update_taux_horaire_garage($infoGarage['taux_horaire_id'],$data['T1'],$data['T2'],$data['T3']);

            return $this->redirectToRoute('garage_index');
        }

        return $this->render('garage/edit.html.twig', [
            'garage' => $garage,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'garage_delete', methods: ['DELETE'])]
    public function delete(Request $request, Garage $garage): Response
    {
        if ($this->isCsrfTokenValid('delete'.$garage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($garage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('garage_index');
    }
}
