<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\CompteRepository")
 */
class Compte
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $numeroCompte;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $solde;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Partenaire", inversedBy="comptes")
     */
    private $partenaire;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="compte")
     */
    private $depots;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comptes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userCreator;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Affectation", mappedBy="compteAffecte")
     */
    private $affectations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="compteDepot")
     */
    private $transactionsDepot;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transaction", mappedBy="compteRetrait")
     */
    private $transactionsRetrait;

    public function __construct()
    {
        $this->depots = new ArrayCollection();
        $this->affectations = new ArrayCollection();
        $this->transactionsDepot = new ArrayCollection();
        $this->transactionsRetrait = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCompte(): ?string
    {
        return $this->numeroCompte;
    }

    public function setNumeroCompte(string $numeroCompte): self
    {
        $this->numeroCompte = $numeroCompte;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSolde(): ?int
    {
        return $this->solde;
    }

    public function setSolde(int $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getPartenaire(): ?Partenaire
    {
        return $this->partenaire;
    }

    public function setPartenaire(?Partenaire $partenaire): self
    {
        $this->partenaire = $partenaire;

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setCompte($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->contains($depot)) {
            $this->depots->removeElement($depot);
            // set the owning side to null (unless already changed)
            if ($depot->getCompte() === $this) {
                $depot->setCompte(null);
            }
        }

        return $this;
    }

    public function getUserCreator(): ?User
    {
        return $this->userCreator;
    }

    public function setUserCreator(?User $userCreator): self
    {
        $this->userCreator = $userCreator;

        return $this;
    }

    /**
     * @return Collection|Affectation[]
     */
    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function addAffectation(Affectation $affectation): self
    {
        if (!$this->affectations->contains($affectation)) {
            $this->affectations[] = $affectation;
            $affectation->setCompteAffecte($this);
        }

        return $this;
    }

    public function removeAffectation(Affectation $affectation): self
    {
        if ($this->affectations->contains($affectation)) {
            $this->affectations->removeElement($affectation);
            // set the owning side to null (unless already changed)
            if ($affectation->getCompteAffecte() === $this) {
                $affectation->setCompteAffecte(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionsDepot(): Collection
    {
        return $this->transactionsDepot;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactionsDepot->contains($transaction)) {
            $this->transactionsDepot[] = $transaction;
            $transaction->setCompteTransite($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactionsDepot->contains($transaction)) {
            $this->transactionsDepot->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getCompteTransite() === $this) {
                $transaction->setCompteTransite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactionsRetrait(): Collection
    {
        return $this->transactionsRetrait;
    }

    public function addTransactionsRetrait(Transaction $transactionsRetrait): self
    {
        if (!$this->transactionsRetrait->contains($transactionsRetrait)) {
            $this->transactionsRetrait[] = $transactionsRetrait;
            $transactionsRetrait->setCompteRetrait($this);
        }

        return $this;
    }

    public function removeTransactionsRetrait(Transaction $transactionsRetrait): self
    {
        if ($this->transactionsRetrait->contains($transactionsRetrait)) {
            $this->transactionsRetrait->removeElement($transactionsRetrait);
            // set the owning side to null (unless already changed)
            if ($transactionsRetrait->getCompteRetrait() === $this) {
                $transactionsRetrait->setCompteRetrait(null);
            }
        }

        return $this;
    }
}
