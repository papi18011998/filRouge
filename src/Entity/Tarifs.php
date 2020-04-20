<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\TarifsRepository")
 */
class Tarifs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $borneMin;

    /**
     * @ORM\Column(type="integer")
     */
    private $borneMax;

    /**
     * @ORM\Column(type="integer")
     */
    private $cout;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorneMin(): ?int
    {
        return $this->borneMin;
    }

    public function setBorneMin(int $borneMin): self
    {
        $this->borneMin = $borneMin;

        return $this;
    }

    public function getBorneMax(): ?int
    {
        return $this->borneMax;
    }

    public function setBorneMax(int $borneMax): self
    {
        $this->borneMax = $borneMax;

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
