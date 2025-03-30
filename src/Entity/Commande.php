<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeImmutable $dateAchat = null;

    #[ORM\Column(length: 255, unique: true)] 
    private ?string $cleAchat = null;

    #[ORM\Column(length: 255, unique: true)] 
    private ?string $cleBillet = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)] 
    private ?PrixOffreEvenement $prixOffreEvenement = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)] 
    private ?Utilisateur $utilisateur = null;

    public function __construct()
    {
        $this->dateAchat = new \DateTimeImmutable(); 
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAchat(): ?\DateTimeImmutable
    {
        return $this->dateAchat;
    }

    public function setDateAchat(\DateTimeImmutable $dateAchat): static
    {
        $this->dateAchat = $dateAchat;

        return $this;
    }

    public function getCleAchat(): ?string
    {
        return $this->cleAchat;
    }

    public function setCleAchat(string $cleAchat): static
    {
        $this->cleAchat = $cleAchat;

        return $this;
    }

    public function getCleBillet(): ?string
    {
        return $this->cleBillet;
    }

    public function setCleBillet(string $cleBillet): static
    {
        $this->cleBillet = $cleBillet;

        return $this;
    }

    public function getPrixOffreEvenement(): ?PrixOffreEvenement
    {
        return $this->prixOffreEvenement;
    }

    public function setPrixOffreEvenement(?PrixOffreEvenement $prixOffreEvenement): static
    {
        $this->prixOffreEvenement = $prixOffreEvenement;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}