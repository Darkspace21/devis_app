<?php

namespace App\Entity;

use App\Repository\GarageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GarageRepository::class)
 */
class Garage
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
    private $nom_garage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $emplacement;

    /**
     * @ORM\OneToOne(targetEntity=TauxHoraire::class, cascade={"persist", "remove"})
     */
    private $TauxHoraire;


    /**
     * @ORM\OneToMany(targetEntity=Forfait::class, mappedBy="garage")
     */
    private $forfait;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="garages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->forfait = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomGarage(): ?string
    {
        return $this->nom_garage;
    }

    public function setNomGarage(string $nom_garage): self
    {
        $this->nom_garage = $nom_garage;

        return $this;
    }

    public function getEmplacement(): ?string
    {
        return $this->emplacement;
    }

    public function setEmplacement(string $emplacement): self
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getRDV(): ?\DateTimeInterface
    {
        return $this->RDV;
    }

    public function setRDV(\DateTimeInterface $RDV): self
    {
        $this->RDV = $RDV;

        return $this;
    }

    public function getTauxHoraire(): ?tauxHoraire
    {
        return $this->TauxHoraire;
    }

    public function setTauxHoraire(?tauxHoraire $TauxHoraire): self
    {
        $this->TauxHoraire = $TauxHoraire;

        return $this;
    }

    /**
     * @return Collection<int, forfait>
     */
    public function getForfait(): Collection
    {
        return $this->forfait;
    }

    public function addForfait(forfait $forfait): self
    {
        if (!$this->forfait->contains($forfait)) {
            $this->forfait[] = $forfait;
            $forfait->setGarage($this);
        }

        return $this;
    }

    public function removeForfait(forfait $forfait): self
    {
        if ($this->forfait->removeElement($forfait)) {
            // set the owning side to null (unless already changed)
            if ($forfait->getGarage() === $this) {
                $forfait->setGarage(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
