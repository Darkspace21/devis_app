<?php

namespace App\Entity;

use App\Repository\TypePrestationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypePrestationRepository::class)
 */
class TypePrestation
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
    private $nom_prestation;

    /**
     * @ORM\OneToMany(targetEntity=ListeOperation::class, mappedBy="typePrestation")
     */
    private $listeOperation;

    /**
     * @ORM\ManyToMany(targetEntity=PiecesNecessaire::class)
     */
    private $pieces_necessaire;

    public function __construct()
    {
        $this->listeOperation = new ArrayCollection();
        $this->pieces_necessaire = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPrestation(): ?string
    {
        return $this->nom_prestation;
    }

    public function setNomPrestation(string $nom_prestation): self
    {
        $this->nom_prestation = $nom_prestation;

        return $this;
    }

    /**
     * @return Collection<int, listeOperation>
     */
    public function getListeOperation(): Collection
    {
        return $this->listeOperation;
    }

    public function addListeOperation(listeOperation $listeOperation): self
    {
        if (!$this->listeOperation->contains($listeOperation)) {
            $this->listeOperation[] = $listeOperation;
            $listeOperation->setTypePrestation($this);
        }

        return $this;
    }

    public function removeListeOperation(listeOperation $listeOperation): self
    {
        if ($this->listeOperation->removeElement($listeOperation)) {
            // set the owning side to null (unless already changed)
            if ($listeOperation->getTypePrestation() === $this) {
                $listeOperation->setTypePrestation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PiecesNecessaire>
     */
    public function getPiecesNecessaire(): Collection
    {
        return $this->pieces_necessaire;
    }

    public function addPiecesNecessaire(PiecesNecessaire $piecesNecessaire): self
    {
        if (!$this->pieces_necessaire->contains($piecesNecessaire)) {
            $this->pieces_necessaire[] = $piecesNecessaire;
        }

        return $this;
    }

    public function removePiecesNecessaire(PiecesNecessaire $piecesNecessaire): self
    {
        $this->pieces_necessaire->removeElement($piecesNecessaire);

        return $this;
    }

}
