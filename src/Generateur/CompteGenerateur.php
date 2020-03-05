<?php
namespace App\Generateur;

use App\Repository\CompteRepository;

class CompteGenerateur
{
 private $numeroCompte;
  public function __construct(CompteRepository $repo) {
      $allComptes = $repo->findAll();
      $this->numeroCompte='C'.date("Y-m-d").(count($allComptes)+1);
      return $this->numeroCompte;
  }  
  public function getNumeroCompte(){
      return $this->numeroCompte;
  }
}
