<?php

namespace App\Entity;

use App\Repository\TauxHoraireRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TauxHoraireRepository::class)
 */
class TauxHoraire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $T1;

    /**
     * @ORM\Column(type="float")
     */
    private $T2;

    /**
     * @ORM\Column(type="float")
     */
    private $T3;

    /**
     * @ORM\ManyToOne(targetEntity=ListeOperation::class, inversedBy="taux_horaire")
     * @ORM\JoinColumn(nullable=false)
     */
    private $listeOperation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getT1(): ?float
    {
        return $this->T1;
    }

    public function setT1(float $T1): self
    {
        $this->T1 = $T1;

        return $this;
    }

    public function getT2(): ?float
    {
        return $this->T2;
    }

    public function setT2(float $T2): self
    {
        $this->T2 = $T2;

        return $this;
    }

    public function getT3(): ?float
    {
        return $this->T3;
    }

    public function setT3(float $T3): self
    {
        $this->T3 = $T3;

        return $this;
    }

    public function getListeOperation(): ?ListeOperation
    {
        return $this->listeOperation;
    }

    public function setListeOperation(?ListeOperation $listeOperation): self
    {
        $this->listeOperation = $listeOperation;

        return $this;
    }
}
