<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $tripDate = null;

    #[ORM\Column]
    private ?int $entrySlot = null;

    #[ORM\Column]
    private ?int $exitSlot = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TimeTable $timeTable = null;

    #[ORM\ManyToOne(inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Driver $mainDriver = null;

    #[ORM\ManyToMany(targetEntity: Driver::class)]
    #[ORM\JoinTable(name: 'trip_passengers')]
    private Collection $passengers;

    public function __construct()
    {
        $this->passengers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTripDate(): ?\DateTimeInterface
    {
        return $this->tripDate;
    }

    public function setTripDate(?\DateTimeInterface $tripDate): self
    {
        $this->tripDate = $tripDate;
        return $this;
    }

    public function getEntrySlot(): ?int
    {
        return $this->entrySlot;
    }

    public function setEntrySlot(?int $entrySlot): self
    {
        $this->entrySlot = $entrySlot;
        return $this;
    }

    public function getExitSlot(): ?int
    {
        return $this->exitSlot;
    }

    public function setExitSlot(?int $exitSlot): self
    {
        $this->exitSlot = $exitSlot;
        return $this;
    }

    public function getTimeTable(): ?TimeTable
    {
        return $this->timeTable;
    }

    public function setTimeTable(?TimeTable $timeTable): self
    {
        $this->timeTable = $timeTable;
        return $this;
    }

    public function getDriver(): ?Driver
    {
        return $this->mainDriver;
    }

    public function setDriver(?Driver $mainDriver): self
    {
        $this->mainDriver = $mainDriver;
        return $this;
    }

    public function getPassengers(): Collection
    {
        return $this->passengers;
    }

    public function addPassenger(Driver $driver): self
    {
        if (!$this->passengers->contains($driver)) {
            $this->passengers->add($driver);
        }

        return $this;
    }
    public function removePassenger(Driver $driver): self
    {
        if ($this->passengers->contains($driver)) {
            $this->passengers->removeElement($driver);
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;
        return $this;
    }
}
