<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateur')] 
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre nom.')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre prénom.')]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre adresse e-mail.')]
    #[Assert\Email(message: 'Votre adresse e-mail n\'est pas valide.')]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $cleUtilisateur = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Commande::class, cascade: ['persist', 'remove'])]
    private Collection $commandes;

   
    private ?string $plainPassword = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $emailVerificationCode = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $emailVerificationRequestedAt = null;

    #[ORM\Column(type: 'boolean')]
    private $isEmailVerified = false; 

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getCleUtilisateur(): ?string
    {
        return $this->cleUtilisateur;
    }

    public function setCleUtilisateur(string $cleUtilisateur): static
    {
        $this->cleUtilisateur = $cleUtilisateur;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
        
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUtilisateur($this);
        }
        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            
            if ($commande->getUtilisateur() === $this) {
                $commande->setUtilisateur(null);
            }
        }
        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getEmailVerificationCode(): ?string
    {
        return $this->emailVerificationCode;
    }

    public function setEmailVerificationCode(?string $emailVerificationCode): self
    {
        $this->emailVerificationCode = $emailVerificationCode;
        return $this;
    }

    public function getEmailVerificationRequestedAt(): ?\DateTimeImmutable
    {
        return $this->emailVerificationRequestedAt;
    }

    public function setEmailVerificationRequestedAt(?\DateTimeImmutable $emailVerificationRequestedAt): self
    {
        $this->emailVerificationRequestedAt = $emailVerificationRequestedAt;
        return $this;
    }

    public function isVerified(): bool 
    {
        return $this->isEmailVerified;
    }

    public function setIsVerified(bool $isEmailVerified): self 
    {
        $this->isEmailVerified = $isEmailVerified;
        return $this;
    }
}