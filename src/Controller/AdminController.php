<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard()
    {
        return $this->render('admin/dashboard.html.twig');
    }


    #[Route('/users', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $valeurs= $this->getUser()->getRoles();
        foreach($valeurs as $valeur ){
            if($valeur== "ROLE_ADMIN"){
                return $this->render('user/index.html.twig', [
                    'users' => $userRepository->findAll(),
                ]);
            }
        }


    }

    #[Route('user/{id}/delete', name: 'user_delete', methods: ['DELETE'])]
    public function delete(Request $request, User $user,UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index')
        ;
    }
}

