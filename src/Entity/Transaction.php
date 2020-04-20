<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\TransactionRepository")
 */
class Transaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $dateTransaction;

    /**
     * @ORM\Column(type="integer")
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codeTransaction;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="transactionsDepot")
     */
    private $compteDepot;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="transactionsRetrait")
     */
    private $compteRetrait;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenomExpediteur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomExpediteur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telExpediteur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenomDestinataire;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomDestinaire;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telDestinataire;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactionsDeposeur")
     */
    private $userDeposeur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transactionsRetrait")
     */
    private $userRetrait;

    /**
     * @ORM\Column(type="integer")
     */
    private $partUserDeposeur;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $partUserRetrait;

    /**
     * @ORM\Column(type="integer")
     */
    private $partEtat;

    /**
     * @ORM\Column(type="integer")
     */
    private $partSysteme;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeTransaction;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateRetrait;

    /**
     * @ORM\Column(type="integer")
     */
    private $cout;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTransaction(): ?\DateTimeInterface
    {
        return $this->dateTransaction;
    }

    public function setDateTransaction(\DateTimeInterface $dateTransaction): self
    {
        $this->dateTransaction = $dateTransaction;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCodeTransaction(): ?string
    {
        return $this->codeTransaction;
    }

    public function setCodeTransaction(string $codeTransaction): self
    {
        $this->codeTransaction = $codeTransaction;

        return $this;
    }

    public function getCompteDepot(): ?Compte
    {
        return $this->compteDepot;
    }

    public function setcompteDepot(?Compte $compteDepot): self
    {
        $this->compteDepot = $compteDepot;

        return $this;
    }

    public function getCompteRetrait(): ?Compte
    {
        return $this->compteRetrait;
    }

    public function setCompteRetrait(?Compte $compteRetrait): self
    {
        $this->compteRetrait = $compteRetrait;

        return $this;
    }

    public function getPrenomExpediteur(): ?string
    {
        return $this->prenomExpediteur;
    }

    public function setPrenomExpediteur(string $prenomExpediteur): self
    {
        $this->prenomExpediteur = $prenomExpediteur;

        return $this;
    }

    public function getNomExpediteur(): ?string
    {
        return $this->nomExpediteur;
    }

    public function setNomExpediteur(string $nomExpediteur): self
    {
        $this->nomExpediteur = $nomExpediteur;

        return $this;
    }

    public function getTelExpediteur(): ?string
    {
        return $this->telExpediteur;
    }

    public function setTelExpediteur(string $telExpediteur): self
    {
        $this->telExpediteur = $telExpediteur;

        return $this;
    }

    public function getPrenomDestinataire(): ?string
    {
        return $this->prenomDestinataire;
    }

    public function setPrenomDestinataire(?string $prenomDestinataire): self
    {
        $this->prenomDestinataire = $prenomDestinataire;

        return $this;
    }

    public function getNomDestinaire(): ?string
    {
        return $this->nomDestinaire;
    }

    public function setNomDestinaire(?string $nomDestinaire): self
    {
        $this->nomDestinaire = $nomDestinaire;

        return $this;
    }

    public function getTelDestinataire(): ?string
    {
        return $this->telDestinataire;
    }

    public function setTelDestinataire(?string $telDestinataire): self
    {
        $this->telDestinataire = $telDestinataire;

        return $this;
    }

    public function getUserDeposeur(): ?User
    {
        return $this->userDeposeur;
    }

    public function setUserDeposeur(?User $userDeposeur): self
    {
        $this->userDeposeur = $userDeposeur;

        return $this;
    }

    public function getUserRetrait(): ?User
    {
        return $this->userRetrait;
    }

    public function setUserRetrait(?User $userRetrait): self
    {
        $this->userRetrait = $userRetrait;

        return $this;
    }

    public function getPartUserDeposeur(): ?int
    {
        return $this->partUserDeposeur;
    }

    public function setPartUserDeposeur(int $partUserDeposeur): self
    {
        $this->partUserDeposeur = $partUserDeposeur;

        return $this;
    }

    public function getPartUserRetrait(): ?int
    {
        return $this->partUserRetrait;
    }

    public function setPartUserRetrait(?int $partUserRetrait): self
    {
        $this->partUserRetrait = $partUserRetrait;

        return $this;
    }

    public function getPartEtat(): ?int
    {
        return $this->partEtat;
    }

    public function setPartEtat(int $partEtat): self
    {
        $this->partEtat = $partEtat;

        return $this;
    }

    public function getPartSysteme(): ?int
    {
        return $this->partSysteme;
    }

    public function setPartSysteme(int $partSysteme): self
    {
        $this->partSysteme = $partSysteme;

        return $this;
    }

    public function getTypeTransaction(): ?string
    {
        return $this->typeTransaction;
    }

    public function setTypeTransaction(string $typeTransaction): self
    {
        $this->typeTransaction = $typeTransaction;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->dateRetrait;
    }

    public function setDateRetrait(?\DateTimeInterface $dateRetrait): self
    {
        $this->dateRetrait = $dateRetrait;

        return $this;
    }

    public function getCout(): ?int
    {
        return $this->cout;
    }

    public function setCout(int $cout): self
    {
        $this->cout = $cout;

        return $this;
    }
}
