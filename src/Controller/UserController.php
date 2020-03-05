<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Repository\RoleRepository;
use App\Generateur\CompteGenerateur;
use App\Repository\CompteRepository;
use App\Repository\PartenaireRepository;
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
        if ($user->getRole()->getLibelle()==="SUPER_ADMIN" || ($this->getUser()->getRole()->getLibelle()==="ADMIN" &&  $user->getRole()->getLibelle()==="ADMIN") || ($this->getUser()->getRole()->getLibelle()==="CAISSIER" &&  $user->getRole()->getLibelle()==="ADMIN_PARTENAIRE")) {
            $find=[
              'status'=> 403,
              'message'=> "Vous n'avez pas le droit requis pour ajouter ce type d'utilisateur"
            ];
            return new JsonResponse($find, 403);
            }
            else{
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
    /**
     * @Route("/compte", name="compte", methods={"POST"})
     */
public function addNewAccount(RoleRepository $repoRole,CompteGenerateur $compteGenerateur,Request $request,SerializerInterface $serializer,CompteRepository $repoCompte,PartenaireRepository $repoPartenaire,UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager)
{
  // test des droits de l'utilisateur connecté  
  $this->denyAccessUnlessGranted('ROLE_ADMIN',null,"Vos droits ne sont pas suffisant pour creer un compte");
  // obtention des données entrees du compte
  $compte=json_decode($request->getContent());
  // recherche du partenaire entre
  $partenaireSearch= $repoPartenaire->findOneByNinea($compte->partenaire->ninea);
  // si le partenaire n'existe pas
  if ($partenaireSearch===null) {
     $newPartenaire = new Partenaire();
     $newPartenaire->setNinea($compte->partenaire->ninea)
                   ->setRc($compte->partenaire->rc);
    // creation du user partenaire
    $role = $repoRole->findOneByLibelle("ADMIN_PARTENAIRE");
    $userPartenaire = new User();
    $userPartenaire->setPrenom($compte->user->prenom)
                   ->setNom($compte->user->nom)
                   ->setEmail($compte->user->email)
                   ->setPassword($encoder->encodePassword($userPartenaire,"passer@2"))
                   ->setRole($role);
    //dd($userPartenaire);
     // si le solde du compte est inferieur a 500000
     if ($compte->solde < 500000) {
       $erreurSolde=[
         "status" => 403,
         "message" => "Le solde par defaut doit etre egal ou superieur a 500000"
       ];
       return new JsonResponse($erreurSolde,403);
     }else {
        // creation du compte
      $newCompte = new Compte();
      $numeroCompte=$compteGenerateur->getNumeroCompte();
      $newCompte->setNumeroCompte($numeroCompte)
                ->setCreatedAt( new \DateTime())
                ->setUserCreator($this->getUser());
     dd($newCompte);
     }
  }
  // si le partenaire existe 
  else {
    $partenaireTrouve=$partenaireSearch;
    dd($partenaireTrouve);
  }
}
}