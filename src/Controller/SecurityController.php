<?php

namespace App\Controller;

use dump;
use App\Entity\Role;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

/**
* @Route("/api")
*/
class SecurityController extends AbstractController
{
    /**
     * @Route("/add", name="security")
     */
    public function index(Request $request,Security $security)
    {   $this->denyAccessUnlessGranted('ROLE_ADMIN',null,"Vos droits ne sont pas suffisant pour creer un partenaire");
        // $user=json_decode($request->getContent());
        // $userRole =$user->role;
        // $role= new Role();
        // $role->setLibelle($userRole);
        // $userCreate =new User();
        // $userCreate->setPrenom($user->prenom)
        // ->setNom($user->nom)
        // ->setEmail($user->email)
        // ->setRole($role)
        // ->setPassword($user->password)
        // ->setIsActive($user->isActive);

        dump($this->getUser()->getRoles());
        
     }
    }
