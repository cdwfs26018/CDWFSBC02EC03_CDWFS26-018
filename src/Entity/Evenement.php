<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private string $titre;

    #[ORM\Column]
    private \DateTimeImmutable $dateEvenement;

    #[ORM\ManyToOne(inversedBy: 'evenements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $responsable = null;

    /** @var Collection<int, Participation> */
    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Participation::class)]
    private Collection $participations;

    /** @var Collection<int, Avis> */
    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Avis::class)]
    private Collection $avis;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDateEvenement(): \DateTimeImmutable
    {
        return $this->dateEvenement;
    }

    public function setDateEvenement(\DateTimeImmutable $dateEvenement): static
    {
        $this->dateEvenement = $dateEvenement;
        return $this;
    }

    public function getResponsable(): ?User
    {
        return $this->responsable;
    }

    public function setResponsable(User $responsable): static
    {
        $this->responsable = $responsable;
        return $this;
    }
}
