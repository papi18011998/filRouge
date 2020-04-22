<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\Depot;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Generateur\CompteGenerateur;
use App\Repository\CompteRepository;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
  //------------------------- Partie ajout des utilisateurs----------------------//
    /**
     * @Route("/users", name="security", methods={"POST"})
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
            return new JsonResponse($find, 403,[],true);
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
//------------------------- Partie ajout de comptes----------------------//
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
      if ($partenaireSearch==null) {
         $newPartenaire = new Partenaire();
         $newPartenaire->setNinea($compte->partenaire->ninea)
                       ->setRc($compte->partenaire->rc);
         //Ajout du partenaire
         $manager->persist($newPartenaire);
        // creation du user partenaire
        $role = $repoRole->findOneByLibelle("ADMIN_PARTENAIRE");
        $userPartenaire = new User();
        $userPartenaire->setPrenom($compte->user->prenom)
                       ->setNom($compte->user->nom)
                       ->setEmail($compte->user->email)
                       ->setPassword($encoder->encodePassword($userPartenaire,"passer@2"))
                       ->setTel($compte->user->tel)
                       ->setRole($role)
                       ->setPartenaire($newPartenaire)
                       ->setIsActive(true);
        $manager->persist($userPartenaire);
         // si le solde du compte est inferieur a 500000 ou superieur a 1000000
         if ($compte->solde < 500000 || $compte->solde > 1000000) {
           $erreurSolde=[
             "status" => 403,
             "message" => "Le solde par defaut doit etre egal ou superieur a 500000 et inferieur ou egal a 1000000"
           ];
           return new JsonResponse($erreurSolde,403);
         }else {
            // creation du compte
          $newCompte = new Compte();
          $numeroCompte=$compteGenerateur->getNumeroCompte();
          $newCompte->setNumeroCompte($numeroCompte)
                    ->setCreatedAt( new \DateTime())
                    ->setUserCreator($this->getUser())
                    ->setSolde($compte->solde)
                    ->setPlafond(1000000)
                    ->setPartenaire($newPartenaire);
          // enregistrement du depot initial
          $newDepot = new Depot();
          $newDepot->setDateDepot(new \DateTime())
                   ->setHeureDepot(new \DateTime())
                   ->setMontant($compte->solde)
                   ->setCompte($newCompte)
                   ->setUser($this->getUser());
         $manager->persist($newDepot);
         $manager->persist($newCompte);
         }
         $manager->flush();
      }
      // si le partenaire existe 
      else {
        $partenaireTrouve= $partenaireSearch;
         // si le solde du compte est inferieur a 500000 ou superieur a 1000000
         if ($compte->solde < 500000 || $compte->solde > 1000000) {
          $erreurSolde=[
            "status" => 403,
            "message" => "Le solde par defaut doit etre egal ou superieur a 500000 et inferieur ou egal a 1000000"
          ];
          return new JsonResponse($erreurSolde,403);
        }
        //si le solde est superieur a 500000
        else {
          // creation du compte
        $newCompte = new Compte();
        $numeroCompte=$compteGenerateur->getNumeroCompte();
        $newCompte->setNumeroCompte($numeroCompte)
                  ->setCreatedAt( new \DateTime())
                  ->setUserCreator($this->getUser())
                  ->setSolde($compte->solde)
                  ->setPlafond(1000000)
                  ->setPartenaire($partenaireSearch);
        // enregistrement du depot initial
        $newDepot = new Depot();
        $newDepot->setDateDepot(new \DateTime())
                 ->setHeureDepot(new \DateTime())
                 ->setMontant($compte->solde)
                 ->setCompte($newCompte)
                 ->setUser($this->getUser());
       $manager->persist($newDepot);
       $manager->persist($newCompte);
       }
       $manager->flush();
      }
      $accountCreated=[
        "status" => 200,
        "message" => 'Votre vient d etre creer avec un solde de '.$newDepot->getMontant().' FCFA'
      ];
      return new JsonResponse($accountCreated,200);
    }
    
//------------------------- Filtres des roles----------------------//
 /**
  * @Route("/roles", methods={"GET"})
  */
 public function getAllRoles(RoleRepository $repoRole,SerializerInterface $serializer){
   if($this->getUser()->getRole()->getLibelle()==="SUPER_ADMIN"){
    $roles=$repoRole->findAll();
    $roleJsonFormat=$serializer->serialize($roles,'json',['groups' => 'role']);
    return new JsonResponse($roleJsonFormat,200,[],true);
   }elseif($this->getUser()->getRole()->getLibelle()==="ADMIN"){
    $roles=$repoRole->getAdminRoleView();
    $roleJsonFormat=$serializer->serialize($roles,'json',['groups' => 'role']);
    return new JsonResponse($roleJsonFormat,200,[],true);
   }elseif($this->getUser()->getRole()->getLibelle()==="ADMIN_PARTENAIRE"){
    $roles=$repoRole->getAdminPartnerRoleView();
    $roleJsonFormat=$serializer->serialize($roles,'json',['groups' => 'role']);
    return new JsonResponse($roleJsonFormat,200,[],true);
   }
 }
 //------------------------- Filtre des utilisateurs----------------------//
 /**
  * @Route("/users", methods={"GET"})
  */
  public function getUsers(UserRepository $repoUser,SerializerInterface $serializer){
    if ($this->getUser()->getRole()->getLibelle()==="SUPER_ADMIN") {
      $users = $repoUser->findAll();
      $userJsonFormat = $serializer->serialize($users,'json',['groups'=>'user']);
      return new JsonResponse($userJsonFormat,200,[],true);
    }elseif ($this->getUser()->getRole()->getLibelle()==="ADMIN") {
      $users = $repoUser->getAdminView();
      $userJsonFormat = $serializer->serialize($users,'json',['group'=>'user']);
      return new JsonResponse($userJsonFormat,200,[],true);
    }elseif($this->getUser()->getRole()->getLibelle()==="ADMIN_PARTENAIRE"){
      $users=$repoUser->getAdminPartnerView();
      $userJsonFormat=$serializer->serialize($users,'json',['groups' => 'user']);
      return new JsonResponse($userJsonFormat,200,[],true);
     }
  }
  //------------------------- Partie depots sur les comptes----------------------//
 /**
  * @Route("/depots", methods={"POST"})
  */
  public function faireDepot(Request $request,SerializerInterface $serializer,EntityManagerInterface $manager){
    // mise en place de la restriction de l'utilisateur connecte en fonction de son role
    $this->denyAccessUnlessGranted('ROLE_CAISSIER',null,"Vos droits ne sont pas suffisant pour faire un depot");
    $depot=$serializer->deserialize($request->getContent(),Depot::class,'json');
    // recueil de l'utilisateur ayant fait le depot d'argent
    $depot->setUser($this->getUser());
    // verification du montant à deposer
      // si le montant saisi est inferieur a 0
    if ($depot->getMontant() <=0) {
      $montantIncorrect = [
        "status" => 403,
        "message" =>"Le montant à déposer doit etre superieur à 0 FCFA"
      ];
      return new JsonResponse($montantIncorrect,403);
    }//sinon si le montant saisi est superieur a 0
    else{
      //Ajout du montant a deposer sur le solde actuel du compte correspondant
      $correspondingAccount = $depot->getCompte();
      $newSolde = $correspondingAccount->getSolde() + $depot->getMontant();
      //mis a jour du compte
      $correspondingAccount->setSolde($newSolde);
      $manager->persist($correspondingAccount);
      // comptabilisation du depot dans la base de donnees
      $depot->setDateDepot(new \DateTime())
            ->setHeureDepot(new \DateTime())
            ->setMontant($depot->getMontant())
            ->setUser($this->getUser());
      $manager->persist($depot);
      $manager->flush();
      $succesDepot = [
        "status" => 200,
        "message" =>'Le depot de '.$depot->getMontant().'FCFA sur le compte '.$correspondingAccount->getNumeroCompte().' s est bien passe'
      ];
      return new JsonResponse($succesDepot,200);
    }
  }
  //-------------------------Recherche du partenaire en fonction de son ninea----//
  /**
   * @Route("/ninea", methods={"POST"})
   */
  public function getNinea(Request $request,PartenaireRepository $repoPartenaire,SerializerInterface $serializer){
    $partenaire=json_decode($request->getContent());
    $getPartenaire = $repoPartenaire->TrouveParNinea($partenaire->ninea);
    if ($getPartenaire == null) {
      $nullPartenaire = [null];
      return new JsonResponse($nullPartenaire,200);
    } else {
      $getPartenaireJsonFormat = $serializer->serialize($getPartenaire,'json');
      return new JsonResponse($getPartenaireJsonFormat,200,[],true);
    }
    
  }
  //------------------------- Obtention de l'utilisateur connecte----------------//
  /**
   * @Route("/user", methods={"GET"})
   */
  public function getConnectedUser(SerializerInterface $serializer)
  {
    $userConnectedJsonFormat = $serializer->serialize($this->getUser(),'json',['groups'=>'user']); 
    return new JsonResponse($userConnectedJsonFormat,200,[],true);
  }
}
