<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Generateur\TarifTransfert;
use Doctrine\ORM\EntityManagerInterface;
use App\Generateur\TransactionGenerateur;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
     * @Route("/api")
     */
class PartenaireController extends AbstractController
{
//     /**
//      * @Route("/partenaires", name="partenaire")
//      */
//     public function addNewPartenaire(){
//     }
//----------------------------Partie des operations sur les comptes------//
          //----------------------      Envoi d'argent      -------------//
/**
 * @Route("/transactions", methods={"POST"})
 */
 public function transaction(TarifTransfert $cout,TransactionGenerateur $code,Request $request,SerializerInterface $serialize,EntityManagerInterface $manager) {
      //recueil des donnees de la transaction
      $transaction = $serialize->deserialize($request->getContent(),Transaction::class,'json');
      $coutTransaction = $cout->coutTransfert($transaction->getMontant());
      //verification du montant a envoyer et le solde du compte du user
      if ($transaction->getCompteDepot()->getSolde() >= $transaction->getMontant() && $coutTransaction !== null) {
          //retrait du montant d'envoi sur le compte correspondant
          $compteDepot = $transaction->getCompteDepot();
          //calcul du restant sur le compte et affectation de la somme restante sur le compte d'envoi
          $soldeRestant = $compteDepot->getSolde() - $transaction->getMontant();
          $compteDepot->setSolde($soldeRestant);
          //passage à null du user qui fait le retrait
          $transaction->setUserRetrait(null);
          // creation et remplissage de la transaction
          $transactionDepot = new Transaction();
          $transactionDepot->setDateTransaction(new \DateTime())
                           ->setMontant($transaction->getMontant())
                           ->setCodeTransaction($code->getNumeroTransaction())
                           ->setCompteDepot($compteDepot)
                           ->setCompteRetrait(null)
                           ->setUserDeposeur($this->getUser())
                           ->setUserRetrait($transaction->getUserRetrait())
                           ->setPrenomExpediteur($transaction->getPrenomExpediteur())
                           ->setNomExpediteur($transaction->getNomExpediteur())
                           ->setTelExpediteur($transaction->getTelExpediteur())
                           ->setPrenomDestinataire($transaction->getPrenomDestinataire())
                           ->setNomDestinaire($transaction->getNomDestinaire())
                           ->setTelDestinataire($transaction->getTelDestinataire())
                           ->setCout($coutTransaction);
          //ditribution des parts
          $coutTransaction = $transactionDepot->getCout();
          //part Etat
          $partEtat = $coutTransaction * 0.4;
          //part systeme
          $partSysteme = $coutTransaction * 0.3;
          //part user envoyeur
          $partEnvoi = $coutTransaction * 0.1;
          //part user retrait
          $partRetrait = $coutTransaction * 0.2;
          $transactionDepot->setPartUserDeposeur($partEnvoi)
                           ->setPartUserRetrait($partRetrait)
                           ->setPartEtat($partEtat)
                           ->setPartSysteme($partSysteme)
                           ->setTypeTransaction('envoi');
          $manager->persist($transactionDepot);
          //modification du compte ou on a fait le depot
          $manager->persist($compteDepot);
          $manager->flush();
          $succesEnvoi = [
               "status" => "200",
               "message" => 'Transfert de'.$transaction->getMontant().'fait avec succès'
           ];
           return new JsonResponse($succesEnvoi,200);
      }elseif($transaction->getCompteDepot()->getSolde() <= 0){
          $erreurMontant = [
               "status" => "403",
               "message" => "Vérifier le montant saisi"
           ];
           return new JsonResponse($erreurMontant,403);
      }elseif($cout->coutTransfert($transaction->getMontant()) === null){
          $erreurLimite = [
               "status" => "403",
               "message" => "Impossible de faire une opération de depot barème dépassé"
           ];
           return new JsonResponse($erreurLimite,403);
      }
      else {
           $erreurCompte = [
               "status" => "403",
               "message" => "Impossible de faire une opération de dépot solde insuffisant"
           ];
           return new JsonResponse($erreurCompte,403);
      }
 }
}
