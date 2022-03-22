<?php

namespace App\Entity;

use App\Repository\ListeOperationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ListeOperationRepository::class)
 */
class ListeOperation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom_operation;

    /**
     * @ORM\Column(type="time")
     */
    private $temps_total;

    /**
     * @ORM\Column(type="float")
     */
    private $prix_operation;

    /**
     * @ORM\ManyToOne(targetEntity=TypePrestation::class, inversedBy="listeOperation")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typePrestation;

    /**
     * @ORM\OneToMany(targetEntity=TauxHoraire::class, mappedBy="listeOperation")
     */
    private $taux_horaire;

    public function __construct()
    {
        $this->taux_horaire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomOperation(): ?string
    {
        return $this->nom_operation;
    }

    public function setNomOperation(string $nom_operation): self
    {
        $this->nom_operation = $nom_operation;

        return $this;
    }

    public function getTempsTotal(): ?\DateTimeInterface
    {
        return $this->temps_total;
    }

    public function setTempsTotal(\DateTimeInterface $temps_total): self
    {
        $this->temps_total = $temps_total;

        return $this;
    }

    public function getPrixOperation(): ?float
    {
        return $this->prix_operation;
    }

    public function setPrixOperation(float $prix_operation): self
    {
        $this->prix_operation = $prix_operation;

        return $this;
    }

    public function getTypePrestation(): ?TypePrestation
    {
        return $this->typePrestation;
    }

    public function setTypePrestation(?TypePrestation $typePrestation): self
    {
        $this->typePrestation = $typePrestation;

        return $this;
    }

    /**
     * @return Collection<int, tauxHoraire>
     */
    public function getTauxHoraire(): Collection
    {
        return $this->taux_horaire;
    }

    public function addTauxHoraire(tauxHoraire $tauxHoraire): self
    {
        if (!$this->taux_horaire->contains($tauxHoraire)) {
            $this->taux_horaire[] = $tauxHoraire;
            $tauxHoraire->setListeOperation($this);
        }

        return $this;
    }

    public function removeTauxHoraire(tauxHoraire $tauxHoraire): self
    {
        if ($this->taux_horaire->removeElement($tauxHoraire)) {
            // set the owning side to null (unless already changed)
            if ($tauxHoraire->getListeOperation() === $this) {
                $tauxHoraire->setListeOperation(null);
            }
        }

        return $this;
    }
}
