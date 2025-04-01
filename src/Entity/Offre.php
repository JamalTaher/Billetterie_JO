<?php

namespace App\Entity;

use App\Repository\OffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $capacite = null;

    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'offre')]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: 'offre', targetEntity: PrixOffreEvenement::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $prixOffreEvenements;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;

        return $this;
    }

  
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setOffre($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            
            if ($commande->getOffre() === $this) {
                $commande->setOffre(null);
            }
        }

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
            $prixOffreEvenement->setOffre($this);
        }

        return $this;
    }

    public function removePrixOffreEvenement(PrixOffreEvenement $prixOffreEvenement): static
    {
        if ($this->prixOffreEvenements->removeElement($prixOffreEvenement)) {
            
            if ($prixOffreEvenement->getOffre() === $this) {
                $prixOffreEvenement->setOffre(null);
            }
        }

        return $this;
    }
}