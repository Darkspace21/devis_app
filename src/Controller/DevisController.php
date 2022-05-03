<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Entity\TypePrestation;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use App\Repository\GarageRepository;
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
            $presta=$_POST['TypePrestation'];
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
                //dump($marque,$modele,$version,$presta);
                $ktpynr=$devisRepository->ktypnr($marque,$modele,$version);
                dump($ktpynr);
                $liste_pieces=$devisRepository->pieces_necessaire($presta);
                dump($liste_pieces);
                return $this->redirectToRoute('devis_new_liste_pieces', array('ktpynr' => $ktpynr,'liste_pieces'=>$liste_pieces));
            }
            //afficher la liste des offres pour le véhicule et le type de presta
            // envoyer le type de presta puis toutes les infos de la voiture afin de retourner les différent garage et les différents prix des garages
            // le prix proposé est egal au prix de chaque pieces + le taux horaire de chaque pieces * le temps pour remplacer la piece.

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

    #[Route('/new/liste_pieces/', name: 'devis_new_liste_pieces', methods: ['GET', 'POST'])]
    public function listePieces(): Response
    {
        $ktypnr=$_GET['ktpynr'];
        $liste_pieces=$_GET['liste_pieces'];
        //dump($ktypnr,$liste_pieces);
        
        return $this->render('devis/new/liste_pieces.html.twig', [
            'ktypnr' =>$ktypnr ,
            'listePieces'=>$liste_pieces
        ]);
    }
    #[Route('/new/liste_pieces/final', name: 'final', methods: ['GET', 'POST'])]
    public function final(DevisRepository $devisRepository, GarageRepository $garageRepository): Response
    {
        // recuperer les pices obligatoire et les pices choisi par le user
        $liste_pieces2=$_POST['piece2'];
        $liste_pieces=$_POST['piece'];
        // recuperer l'id vehicule
        $ktypnr=$_POST['ktypnr'];

        // on addition les 2 tableaux
        $liste_pieces_total=array_merge($liste_pieces, $liste_pieces2);
        //dump($liste_pieces_total);

        // récuperer le type de produits que veut l'utilisateur
        $gamme_produit=$this->getUser()->getGammeProduit();

        // recuperer les infos sur la piece
        $info_piece=$devisRepository->prix_piece($liste_pieces,$ktypnr);
        $info_piece2=$devisRepository->prix_piece($liste_pieces2,$ktypnr);
        
        //recuperer le temps et le taux horaire
        $temps_piece=$devisRepository->temps_prestation($liste_pieces[0]);
        $temps_piece2=$devisRepository->temps_prestation($liste_pieces2[0]);


        $info_piece_total= array_merge($info_piece, $temps_piece);
        dump($info_piece_total);
        //recuperer la liste des garages
        $liste_garage = $garageRepository->liste_garage();
        //dump($liste_pieces,$liste_pieces2,$ktypnr,$gamme_produit,$info_piece,$info_piece2,$temps_piece,$temps_piece2,$liste_garage);
        
        /* charger la liste des garages et leurs prix * temps de modification + ajouter le prix du produit
        charger la liste des resultats + renvoyer vers devis 
        a optimiser recuperer et mettre dans un tableau les différentes pieces
        */
        
        return $this->render('devis/new/liste_pieces/final.html.twig', [
            'ktypnr' =>$ktypnr ,
            'listePieces'=>$liste_pieces,
            'listePieces2'=>$liste_pieces2,
            'info_piece'=>$info_piece,
            'info_piece2'=>$info_piece2,
            'temps_piece'=>$temps_piece,
            'temps_piece2'=>$temps_piece2,
            'liste_garage'=>$liste_garage
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
