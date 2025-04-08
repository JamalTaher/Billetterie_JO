<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lieu = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: PrixOffreEvenement::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $prixOffreEvenements;

    public function __construct()
    {
        $this->prixOffreEvenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

   
    public function getPrixOffreEvenements(): Collection
    {
        return $this->prixOffreEvenements;
    }

    public function addPrixOffreEvenement(PrixOffreEvenement $prixOffreEvenement): static
    {
        if (!$this->prixOffreEvenements->contains($prixOffreEvenement)) {
            $this->prixOffreEvenements->add($prixOffreEvenement);
            $prixOffreEvenement->setEvenement($this);
        }

        return $this;
    }

    public function removePrixOffreEvenement(PrixOffreEvenement $prixOffreEvenement): static
    {
        if ($this->prixOffreEvenements->removeElement($prixOffreEvenement)) {
            
            if ($prixOffreEvenement->getEvenement() === $this) {
                $prixOffreEvenement->setEvenement(null);
            }
        }

        return $this;
    }

    
    public function createPrixOffreEvenement(): PrixOffreEvenement
    {
        $prixOffreEvenement = new PrixOffreEvenement();
        $prixOffreEvenement->setEvenement($this);
        $this->prixOffreEvenements->add($prixOffreEvenement);
        return $prixOffreEvenement;
    }
}