<?php

namespace App\Controller;

use Twilio\Rest\Client;
use App\Entity\Transaction;
use App\Generateur\TarifTransfert;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Generateur\TransactionGenerateur;
use App\Repository\TransactionRepository;
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
           //----------envoi du sms avec le code de transaction en utilisant nexmo-----//
           $basic  = new \Nexmo\Client\Credentials\Basic('3aa4c221', 't4H1jn87prl9rHv8');
           $client = new \Nexmo\Client($basic);
          try {
               $message = $client->message()->send([
                   'to' => '221'.$transactionDepot->getTelDestinataire(),
                   'from' => 'Picash',
                   'text' => 'Cher client Picash, vous venez de recevoir '
                   .$transactionDepot->getMontant().' FCFA de la part de '
                   .$transactionDepot->getPrenomExpediteur().' '.$transactionDepot->getNomExpediteur().
                   '. Le code de transaction est: '.$transactionDepot->getCodeTransaction().
                   '. Merci de la confiance que vous nous accordez.'
               ]);
               $response = $message->getResponseData();
           
               if($response['messages'][0]['status'] == 0) {
                   echo "The message was sent successfully\n";
               } else {
                   echo "The message failed with status: " . $response['messages'][0]['status'] . "\n";
               }
           } catch (Exception $e) {
               echo "The message was not sent. Error: " . $e->getMessage() . "\n";
           }
        // $account_sid = 'ACf213963cb01ffe8e1a5fd43b2b97b350';
        // $auth_token = '4d66b7eb125ccce0db77eadd045e5b24';

        // $twilio_number = "+12729992660";

        // $client = new Client($account_sid, $auth_token);
        // $client->messages->create(
        //         '+221'.$transaction->getTelDestinataire(),
        //     array(
        //         'from' => $twilio_number,
        //         'body' => 'Cher client Picash, vous venez de recevoir '
        //                     .$transactionDepot->getMontant().' FCFA de la part de '
        //                     .$transactionDepot->getPrenomExpediteur().' '.$transactionDepot->getNomExpediteur().
        //                     '. Le code de transaction est: '.$transactionDepot->getCodeTransaction().
        //                      '. Merci de la confiance que vous nous accordez.'
        //     )
        // );


           return new JsonResponse($succesEnvoi,200);
      }elseif($transaction->getMontant() <= 0){
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
    //---------------Retrait d'argent envoye sur un autre compte ou le meme compte-------//
/**
 * @Route("/transactions", methods={"PATCH"})
 */
public function retrait(EntityManagerInterface $manager,Request $request,TransactionRepository $repoTransaction,CompteRepository $compteRepo){
    //verification des droits de retrait sur un compte
    $retrait = json_decode($request->getContent());
    //recherche du code de transaction entre par le user partenaire qui fait le retrait
     $transaction = $repoTransaction->findOneByCodeTransaction($retrait->codeTransaction);
     //verification du code de transaction passe en parametre
    if ($transaction == null) {
       $notExistCodeTransaction = [
            "status" => 404,
            "meassage" =>"Le code de transaction tapé n'existe pas"
       ];
       return new JsonResponse($notExistCodeTransaction,404);
    }
    //si le code existe  mais que le retrait est fait
    elseif($transaction != null && $transaction->getTypeTransaction()=="retrait") {
        $alreadyExtract = [
            "status" => 403,
            "meassage" =>'Le retrait de '.$transaction->getMontant().' a déjà été fait'
       ];
       return new JsonResponse($alreadyExtract,404);

    }//si le code existe  mais que le reatrait n'est pas fait
    elseif ($transaction != null && $transaction->getTypeTransaction()=="envoi") {
            //recherche du compte
            $compteRetrait = $compteRepo->findOneByNumeroCompte($retrait->numeroCompte);
            // si le numero existe
            if ($compteRetrait == null) {
                $notExistNumeroCompte = [
                    "status" => 404,
                    "meassage" =>"Le numéro de compte tapé n'existe pas"
               ];
               return new JsonResponse($notExistNumeroCompte,404);
            } // si le compte existe
            else {
               //verification de la possibilite de faire ce retrait
               $total = $compteRetrait->getSolde() + $transaction->getMontant();
               //si le plafond est atteint
               if ($compteRetrait->getPlafond() < $total){
                $erreurRetrait = [
                    "status" => 403,
                    "meassage" =>"Vous ne pouvez pas faire ce retrait car le plafond est atteint"
                         ];
               return new JsonResponse($erreurRetrait,404);
               }
               //si le plafond n'est pas encore atteint
               else {
                    //enregistrement du user qui a fait le retrait
                    $transaction->setUserRetrait($this->getUser());
                    //modification du solde du compte
                    $newSolde = $compteRetrait->getSolde() + $transaction->getMontant();
                    $compteRetrait->setSolde($newSolde);
                    //application du retrait dans ce compte
                    $transaction->setCompteRetrait($compteRetrait);
                    $transaction->setDateRetrait(new \DateTime());
                    //modification du type de transaction
                    $transaction->setTypeTransaction("retrait");
                    //modification du solde du compte et enregistrement de la transaction
                    $manager->persist($compteRetrait);
                    $manager->persist($transaction);
                    $manager->flush();
                    $successRetrait = [
                        "status" => 200,
                        "message" => 'Le retrait de '.$transaction->getMontant().' s\'est bien passé'
                    ];
                    //envoi du sms a l'expediteur
                    $basic  = new \Nexmo\Client\Credentials\Basic('3aa4c221', 't4H1jn87prl9rHv8');
                    $client = new \Nexmo\Client($basic);
                   try {
                        $message = $client->message()->send([
                            'to' => '221'.$transaction->getTelExpediteur(),
                            'from' => 'Picash',
                            'text' => 'Cher client Picash, les'
                            .$transaction->getMontant().' FCFA viennent d\'être retiré par'
                            .$transaction->getPrenomDestinataire().' '.$transaction->getNomDestinaire().
                            '. Merci de la confiance que vous nous accordez.'
                        ]);
                        $response = $message->getResponseData();
                    
                        if($response['messages'][0]['status'] == 0) {
                            echo "The message was sent successfully\n";
                        } else {
                            echo "The message failed with status: " . $response['messages'][0]['status'] . "\n";
                        }
                    } catch (Exception $e) {
                        echo "The message was not sent. Error: " . $e->getMessage() . "\n";
                    }
                // $account_sid = 'ACf213963cb01ffe8e1a5fd43b2b97b350';
                // $auth_token = '4d66b7eb125ccce0db77eadd045e5b24';
        
                // $twilio_number = "+12729992660";
        
                // $client = new Client($account_sid, $auth_token);
                // $client->messages->create(
                //         '+221'.$transaction->getTelExpediteur(),
                //     array(
                //         'from' => $twilio_number,
                //         'body' => 'Cher client Picash, les'
                //                     .$transaction->getMontant().' FCFA viennent d\'etre retiré par'
                //                     .$transaction->getPrenomDestinataire().' '.$transaction->getNomDestinaire().
                //                     '. Merci de la confiance que vous nous accordez.'
                //     )
                // );
                    return new JsonResponse($successRetrait,200);
               }
            }
    }
}
// recherche du code de transaction
/**
 * @Route("/getTransactionCode")
 */
public function codeTransaction(){
    
}
}
