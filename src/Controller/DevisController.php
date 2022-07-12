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
        $user_id=$this->getUser()->getId();
        $devis=$devisRepository->liste_devis($user_id);
        //dump($devis);
        return $this->render('devis/index.html.twig', [
            'devis' =>$devis ,
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
        //dump($marque);
        //dump($modele);
        //dump($version);
        //die();
       
        if ($form->isSubmitted()) {
            $data = $form->getData();
            $presta=$_POST['TypePrestation'];
            //dump($_POST['marque']);
            //dump($_POST['TypePrestation']);
            if (isset($_POST['immat'])){
                $immat=strtoupper($_POST['immat']);

                $ktpynr=$devisRepository->ktypnrByImmat($immat);

                $liste_pieces=$devisRepository->pieces_necessaire($presta);

                return $this->redirectToRoute('devis_new_liste_pieces', array('ktpynr' => $ktpynr,'liste_pieces'=>$liste_pieces,'presta'=>$presta));
                // lancer la recherche pour les différents devis
                
            }else{
                $marqueModeleVersion=$_POST['version'];
                $marqueModeleVersion = explode("/", $marqueModeleVersion);
                $marque= $marqueModeleVersion[0];
                $modele= $marqueModeleVersion[1];
                $version= $marqueModeleVersion[2];
                //dump($marque,$modele,$version,$presta);
                $ktpynr=$devisRepository->ktypnr($marque,$modele,$version);
                //dump($ktpynr);
                $liste_pieces=$devisRepository->pieces_necessaire($presta);
                //dump($liste_pieces);
                return $this->redirectToRoute('devis_new_liste_pieces', array('ktpynr' => $ktpynr,'liste_pieces'=>$liste_pieces,'presta'=>$presta));
            }
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
        $presta=$_GET['presta'];
        return $this->render('devis/new/liste_pieces.html.twig', [
            'ktypnr' =>$ktypnr ,
            'listePieces'=>$liste_pieces,
            'presta'=>$presta
        ]);
    }
    #[Route('/new/liste_pieces/resultat', name: 'resultat', methods: ['GET', 'POST'])]
    public function resultat(DevisRepository $devisRepository, GarageRepository $garageRepository): Response
    {
        // recuperer les pices obligatoire et les pices choisi par le user
        $liste_pieces2='';
        if (isset($_GET['piece2'])){
            $liste_pieces2=$_GET['piece2'];
        }
        $liste_pieces=$_GET['piece'];
        // recuperer l'id vehicule
        $ktypnr=$_GET['ktypnr'];
        $presta=$_GET['presta'];
        // dump($presta);
        // on cumul les 2 tableaux
        if (sizeof($liste_pieces) != '' && sizeof($liste_pieces2)!=''){
            $liste_pieces_total=array_merge($liste_pieces, $liste_pieces2);
        }else{
            $liste_pieces_total = $liste_pieces;
        }

        // récuperer le type de produits que veut l'utilisateur
        $gamme_produit=$this->getUser()->getGammeProduit();
        $info_piece_total=[];
        //dump($liste_pieces_total);
        $liste_pieces_info_final='';
        for($i = 0; $i <= sizeof($liste_pieces_total)-1; $i++){
            // recuperer les infos sur la piece
            //dump($i);
            $info_piece=$devisRepository->prix_piece($liste_pieces_total[$i],$ktypnr);

             //recuperer le temps et le taux horaire
            $temps_piece=$devisRepository->temps_prestation($liste_pieces_total[$i]);
            
            $info_piece_total= array_merge($info_piece, $temps_piece);
            //dump($info_piece_total);
            $test=json_encode($info_piece_total);
            if ($liste_pieces_info_final =='' ){
                $liste_pieces_info_final = $test;
            }else{
                $liste_pieces_info_final= $liste_pieces_info_final .','.$test ;
            }
        }
        $liste_pieces_info_final= json_decode('['.$liste_pieces_info_final.']');
        //dump($liste_pieces_info_final);
        //recuperer la liste des garages avec le taux_horaire
        $liste_garage = $garageRepository->liste_garage();
        //dump($liste_pieces,$liste_pieces2,$ktypnr,$gamme_produit,$info_piece,$info_piece2,$temps_piece,$temps_piece2,$liste_garage);
        
        /* charger la liste des garages et leurs prix * temps de modification + ajouter le prix du produit
        charger la liste des resultats + renvoyer vers devis 
        a optimiser recuperer et mettre dans un tableau les différentes pieces
        */
        
        return $this->render('devis/new/liste_pieces/resultat.html.twig', [
            'ktypnr' =>$ktypnr ,
            'liste_garage'=>$liste_garage,
            'liste_pieces_info_final'=>$liste_pieces_info_final,
            'presta'=>$presta
        ]);
    }

    #[Route('/new/liste_pieces/resultat/final', name: 'final', methods: ['GET', 'POST'])]
    public function creer_devis(DevisRepository $devisRepository, GarageRepository $garageRepository, TypePrestationRepository $typePrestationRepository): Response
    {
        // on récupere toutes les datas
        $garage_id=$_POST["garage"];
        $pieces=$_POST["piece"];
        $prix_main_oeuvre=$_POST['prix_main_oeuvre'];
        $prix_total=$_POST['prix_total'];
        $presta=$_POST['presta'];
        $date=$_POST['date'];
        $heure=$_POST['heure'];
        //dump($date);
        //dump($heure);
        $presta_id= $typePrestationRepository->type_presta_id($presta);
        $user_id=$this->getUser()->getId();
        
        // on créer le devis
        $devisRepository->creer_devis($user_id,$presta_id['id'],$prix_total,$garage_id,$prix_main_oeuvre,$date,$heure);
        
        //on recupere l'id du devis
        $devis_id=$devisRepository->max_id_devis($user_id);
        //on ajoute les pieces selectionnées dans pieces_choisi afin d'être sur que le prix est figé
        foreach($pieces as $piece){
            list($genartnr, $temps, $nom, $marque, $prix) = explode(" / ", $piece);
            
            $devisRepository->devis_pieces_choisi($genartnr, $temps, $nom, $marque, $prix, $devis_id['id']);
        // message d'ajout d'un devis 
        }
        $message="votre devis ".$devis_id['id']. " a bien été créé";
        return $this->redirectToRoute("devis_index",array('message'=>$message));
        
    }

    #[Route('/{id}', name: 'devis_show', methods: ['GET'])]
    public function show(DevisRepository $devisRepository, $id): Response
    {
        $devis=$devisRepository->info_devis($id);
        $pieces_devis=$devisRepository->info_pieces_devis($id);
        return $this->render('devis/show.html.twig', [
            'devis' => $devis,
            'pieces_devis' => $pieces_devis
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
