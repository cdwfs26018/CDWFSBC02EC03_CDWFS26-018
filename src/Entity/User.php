<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    /** @var Collection<int, Evenement> */
    #[ORM\OneToMany(mappedBy: 'responsable', targetEntity: Evenement::class)]
    private Collection $evenements;

    /** @var Collection<int, Participation> */
    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Participation::class)]
    private Collection $participations;

    /** @var Collection<int, Avis> */
    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Avis::class)]
    private Collection $avisRediges;

    /** @var Collection<int, Avis> */
    #[ORM\OneToMany(mappedBy: 'moderateur', targetEntity: Avis::class)]
    private Collection $avisModeres;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->avisRediges = new ArrayCollection();
        $this->avisModeres = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void {}

    /** @return Collection<int, Avis> */
    public function getAvisRediges(): Collection
    {
        return $this->avisRediges;
    }

    public function addAvisRedige(Avis $avis): static
    {
        if (!$this->avisRediges->contains($avis)) {
            $this->avisRediges->add($avis);
            $avis->setAuteur($this);
        }
        return $this;
    }

    public function removeAvisRedige(Avis $avis): static
    {
        if ($this->avisRediges->removeElement($avis)) {
            if ($avis->getAuteur() === $this) {
                $avis->setAuteur(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Avis> */
    public function getAvisModeres(): Collection
    {
        return $this->avisModeres;
    }

    public function addAvisModere(Avis $avis): static
    {
        if (!$this->avisModeres->contains($avis)) {
            $this->avisModeres->add($avis);
            $avis->setModerateur($this);
        }
        return $this;
    }

    public function removeAvisModere(Avis $avis): static
    {
        if ($this->avisModeres->removeElement($avis)) {
            if ($avis->getModerateur() === $this) {
                $avis->setModerateur(null);
            }
        }
        return $this;
    }
}
