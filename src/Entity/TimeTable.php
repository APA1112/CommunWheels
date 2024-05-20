<?php

namespace App\Entity;

use App\Repository\TimeTableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TimeTableRepository::class)]
class TimeTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $weekStartDate = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'timeTables')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $band = null;

    #[ORM\OneToMany(mappedBy: 'timeTable', targetEntity: Trip::class, orphanRemoval: true)]
    private Collection $trips;

    public function __construct()
    {
        $this->trips = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeekStartDate(): ?\DateTimeInterface
    {
        return $this->weekStartDate;
    }

    public function setWeekStartDate(\DateTimeInterface $weekStartDate): static
    {
        $this->weekStartDate = $weekStartDate;

        return $this;
    }

    public function getBand(): ?Group
    {
        return $this->band;
    }

    public function setBand(?Group $band): static
    {
        $this->band = $band;

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getTrips(): Collection
    {
        return $this->trips;
    }

    public function addTrip(Trip $trip): static
    {
        if (!$this->trips->contains($trip)) {
            $this->trips->add($trip);
            $trip->setTimeTable($this);
        }

        return $this;
    }

    public function removeTrip(Trip $trip): static
    {
        if ($this->trips->removeElement($trip)) {
            // set the owning side to null (unless already changed)
            if ($trip->getTimeTable() === $this) {
                $trip->setTimeTable(null);
            }
        }

        return $this;
    }
}
