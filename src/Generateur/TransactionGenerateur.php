<?php
namespace App\Generateur;

use App\Repository\TransactionRepository;


class TransactionGenerateur{
    private $numeroTransaction;
    public function __construct( TransactionRepository $repoTransaction){
        $allTransaction = $repoTransaction->findAll();
        $this->numeroTransaction = 'T'.date("Ymd").random_int(100, 999).(count($allTransaction)+1);
        return $this->numeroTransaction;
    }
    public function getNumeroTransaction(){
        return $this->numeroTransaction;
    }
}