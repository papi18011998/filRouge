<?php
namespace App\Generateur;

use App\Repository\TarifsRepository;


class TarifTransfert{
    private $bareme;
    private $cout;
    public function __construct(TarifsRepository $repoTarifs){
        $this->bareme = $repoTarifs->findAll();
    }
    public function coutTransfert ($montant)
    {
        foreach ($this->bareme as  $tarif) {
            if ($montant >= $tarif->getBorneMin() && $montant <= $tarif->getBorneMax() ) {
                $this->cout = $tarif->getCout();
                return $this->cout;
            }
        }
    }
}