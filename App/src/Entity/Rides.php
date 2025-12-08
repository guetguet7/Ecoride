<?php

namespace App\Entity;

use App\Repository\RidesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\Avis;

#[ORM\Entity(repositoryClass: RidesRepository::class)]
class Rides
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $pseudo = null; // Pseudo du conducteur enregistré au moment de la création

    #[ORM\Column(type: Types::BLOB)]
    private mixed $photo = null; // Photo (copie du profil conducteur)

    #[ORM\Column(length: 255)]
    private ?string $nbplace = null;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $prix = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $dateHeureDepart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $dateHeureArrivee = null;

    #[ORM\Column(length: 255)]
    private ?string $lieuDepart = null;

    #[ORM\Column(length: 255)]
    private ?string $lieuArrivee = null;

    #[ORM\Column(length: 20, options: ['default' => 'active'])]
    private string $status = 'active'; // active, in_progress, finished

    #[ORM\ManyToOne(inversedBy: 'rides')]
    private ?User $user = null; // Chauffeur

    #[ORM\OneToMany(mappedBy: 'ride', targetEntity: Participation::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $participations;

    #[ORM\OneToMany(mappedBy: 'ride', targetEntity: Avis::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $avis;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPhoto(): mixed
    {
        return $this->photo;
    }

    public function setPhoto(mixed $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getNbplace(): ?string
    {
        return $this->nbplace;
    }

    public function setNbplace(string $nbplace): static
    {
        $this->nbplace = $nbplace;

        return $this;
    }

    public function getPrix(): int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = max(0, $prix);

        return $this;
    }

    public function getDateHeureDepart(): ?\DateTime
    {
        return $this->dateHeureDepart;
    }

    public function setDateHeureDepart(\DateTime $dateHeureDepart): static
    {
        $this->dateHeureDepart = $dateHeureDepart;

        return $this;
    }

    public function getDateHeureArrivee(): ?\DateTime
    {
        return $this->dateHeureArrivee;
    }

    public function setDateHeureArrivee(\DateTime $dateHeureArrivee): static
    {
        $this->dateHeureArrivee = $dateHeureArrivee;

        return $this;
    }

    public function getLieuDepart(): ?string
    {
        return $this->lieuDepart;
    }

    public function setLieuDepart(string $lieuDepart): static
    {
        $this->lieuDepart = $lieuDepart;

        return $this;
    }

    public function getLieuArrivee(): ?string
    {
        return $this->lieuArrivee;
    }

    public function setLieuArrivee(string $lieuArrivee): static
    {
        $this->lieuArrivee = $lieuArrivee;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): static
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setRide($this);
        }

        return $this;
    }

    public function removeParticipation(Participation $participation): static
    {
        if ($this->participations->removeElement($participation)) {
            if ($participation->getRide() === $this) {
                $participation->setRide($this);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avis>
     */
    public function getAvis(): Collection
    {
        return $this->avis;
    }

    public function addAvi(Avis $avis): static
    {
        if (!$this->avis->contains($avis)) {
            $this->avis->add($avis);
            $avis->setRide($this);
        }

        return $this;
    }

    public function removeAvi(Avis $avis): static
    {
        if ($this->avis->removeElement($avis)) {
            if ($avis->getRide() === $this) {
                $avis->setRide(null);
            }
        }

        return $this;
    }
}
