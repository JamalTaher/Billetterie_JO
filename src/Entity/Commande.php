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

    #[ORM\Column(length: 255, unique: true)] // Assurez-vous que chaque clé d'achat est unique
    private ?string $cleAchat = null;

    #[ORM\Column(length: 255, unique: true)] // Assurez-vous que chaque clé de billet est unique
    private ?string $cleBillet = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)] // Une commande doit toujours être liée à une offre
    private ?Offre $offre = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)] // Une commande doit toujours être liée à un utilisateur
    private ?Utilisateur $utilisateur = null;

    public function __construct()
    {
        $this->dateAchat = new \DateTimeImmutable(); // Initialisation par défaut lors de la création
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

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): static
    {
        $this->offre = $offre;

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

    // La propriété $relation semble superflue, je l'ai supprimée.
    // Si vous aviez une intention spécifique, veuillez me l'indiquer.
}