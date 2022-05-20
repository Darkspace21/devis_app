<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DevisRepository::class)
 */
class Devis
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
    private $prix;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $userId;

    /**
     * @ORM\OneToOne(targetEntity=TypePrestation::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $typePrestation;

    /**
     * @ORM\Column(type="integer")
     */
    private $garage_id;

    /**
     * @ORM\Column(type="float")
     */
    private $main_oeuvre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getNbHeureTotal(): ?string
    {
        return $this->nb_heure_total;
    }

    public function setNbHeureTotal(string $nb_heure_total): self
    {
        $this->nb_heure_total = $nb_heure_total;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user->getId();
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getTypePrestation(): ?typePrestation
    {
        return $this->typePrestation;
    }

    public function setTypePrestation(typePrestation $typePrestation): self
    {
        $this->typePrestation = $typePrestation;

        return $this;
    }

}
