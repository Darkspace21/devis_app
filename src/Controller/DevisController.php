<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\TypePrestation;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use App\Repository\TypePrestationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

#[Route('/devis')]
class DevisController extends AbstractController
{
    #[Route('/', name: 'devis_index', methods: ['GET'])]
    public function index(DevisRepository $devisRepository): Response
    {
        return $this->render('devis/index.html.twig', [
            'devis' => $devisRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'devis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TypePrestationRepository $typePrestationRepository, DevisRepository $devisRepository ): Response
    {
        //$devi = new Devis();
        $defaultData = ['message' => 'Type your message here'];
        //$form = $this->createForm(DevisType::class, $devi);
        //recuperer le type de prestation
        $marque='';
        $modele='';
        $version='';
        if (!isset($modeleF)){
            $marqueF='';
        }
        $marque=$devisRepository->listeMarque();
        $modele=$devisRepository->listeModele();
        $version=$devisRepository->listeVersion();
        // dump($marque);
        $typePresta=$typePrestationRepository->findAll();
        $listePresta=[];
        foreach($typePresta as $valeur){
            array_push($listePresta,$valeur->getNomPrestation());
        }
        //dump($listePresta);
        //dump($typePresta);
        $form = $this->createFormBuilder($defaultData)
            ->add('immat', TextType::class,['required' => false,])
            ->add('marque', TextType::class,['required' => false,])
            ->add('modele', TextType::class,['required' => false,])
            ->add('version', TextType::class,['required' => false,])
            ->add('TypePrestation', ChoiceType::class,[
                'choices'=>[
                    // $listePresta2,
                    $listePresta[0]=>$listePresta[0],
                    $listePresta[1]=>$listePresta[1],
                    $listePresta[2]=>$listePresta[2]
                ]
            ])
            ->getForm();
        $form->handleRequest($request);
        // dump($marque);
        // die();
       
        if ($form->isSubmitted()) {
            $data = $form->getData();
            $data['TypePrestation']=$_POST['TypePrestation'];
            //dump($_POST['marque']);
            //dump($_POST['TypePrestation']);
            if (isset($_POST['immat'])){
                $data['immat']=$_POST['immat'];
                // lancer la recherche pour avoir les infos de la voiture
                // lancer la recherche pour les différents devis
                
            }else{
                $marqueModeleVersion=$_POST['version'];
                $marqueModeleVersion = explode("/", $marqueModeleVersion);
                $marque= $marqueModeleVersion[0];
                $modele= $marqueModeleVersion[1];
                $version= $marqueModeleVersion[2];
                dump($marque,$modele,$version);
                die();
            }
            //afficher la liste des offres pour le véhicule et le type de presta
             
        }
        
        return $this->render('devis/new.html.twig', [
            'devi' => $form,
            'form' => $form->createView(),
            'TypePresta'=>$listePresta,
            'modele'=>$modele,
            'marque'=>$marque,
            'version'=>$version
        ]);
    }

    #[Route('/{id}', name: 'devis_show', methods: ['GET'])]
    public function show(Devis $devi): Response
    {
        return $this->render('devis/show.html.twig', [
            'devi' => $devi,
        ]);
    }

    #[Route('/{id}/edit', name: 'devis_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devis $devi): Response
    {
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('devis_index');
        }

        return $this->render('devis/edit.html.twig', [
            'devi' => $devi,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'devis_delete', methods: ['DELETE'])]
    public function delete(Request $request, Devis $devi): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devi->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($devi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('devis_index');
    }
}
