<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(): Response
    {
        return $this->redirectToRoute('app_register');
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user, $id): Response
    {
        $valeurs= $this->getUser()->getRoles();
        $admin=0;
        foreach($valeurs as $valeur ){
            if($valeur== "ROLE_ADMIN"){
                $admin=1;
            }
        }

        if($this->getUser()->getId()== $id or $admin==1){
            return $this->render('user/show.html.twig', [
                'user' => $user,
            ]);
        }else{
           return $this->redirectToRoute('user_show',['id'=>$this->getUser()->getId()]);
        }
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user,$id): Response
    {
        $message='';
        $valeurs= $this->getUser()->getRoles();
        $admin=0;
        foreach($valeurs as $valeur ){
            if($valeur== "ROLE_ADMIN"){
                $admin=1;
            }
        }
        
        if($this->getUser()->getId()== $id or $admin==1){
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                $message='la mise à jour a bien été éffectué';
            }

            return $this->render('user/edit.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'message'=>$message
            ]);
        }
    }


}
