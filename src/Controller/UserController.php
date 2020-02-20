<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
/**
* @Route("/api")
*/
class UserController extends AbstractController
{
    /**
     * @Route("/users", name="security")
     */
    public function addNewUser(SerializerInterface $serializer,Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager){  
        // test des droits de l'utilisateur connecté  
        $this->denyAccessUnlessGranted('ROLE_ADMIN',null,"Vos droits ne sont pas suffisant pour creer un utilisateur");
      // recupération de l'utilisateur qu'on essaye de créer 
        $user=$serializer->deserialize($request->getContent(),User::class,'json');
        if ($user->getRole()->getLibelle()==="SUPER_ADMIN" || ($this->getUser()->getRole()->getLibelle()==="ADMIN" &&  $user->getRole()->getLibelle()==="ADMIN" )) {
            $find=[
              'status'=> 403,
              'message'=> "Vous n'avez pas le droit requis pour ajouter ce type d'utilisateur"
            ];
            return new JsonResponse($find, 403);
            }else{
                 $user->setPassword($encoder->encodePassword($user,$user->getPassword()));
                 $manager->persist($user);
                 $manager->flush();
                 $find=[
                  'status'=> 200,
                  'message'=> "Ajoute avec succes"
                ];
                 return new JsonResponse($find, 200);
       }
}
}