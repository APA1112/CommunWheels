<?php

namespace App\Entity;

use App\Repository\DriverRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DriverRepository::class)]
class Driver
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private ?int $seats = null;

    #[ORM\Column]
    private ?int $daysDriven = null;

    #[ORM\Column(nullable: true)]
    private ?int $waitTime = null;

    #[ORM\ManyToOne]
    private ?Absence $absences = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Schedule $schedule = null;

    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'drivers')]
    private Collection $groupCollection;

    public function __construct()
    {
        $this->groupCollection = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Driver
    {
        $this->name = $name;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): Driver
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Driver
    {
        $this->email = $email;
        return $this;
    }

    public function getSeats(): ?int
    {
        return $this->seats;
    }

    public function setSeats(?int $seats): Driver
    {
        $this->seats = $seats;
        return $this;
    }

    public function getDaysDriven(): ?int
    {
        return $this->daysDriven;
    }

    public function setDaysDriven(?int $daysDriven): Driver
    {
        $this->daysDriven = $daysDriven;
        return $this;
    }

    public function getWaitTime(): ?int
    {
        return $this->waitTime;
    }

    public function setWaitTime(?int $waitTime): Driver
    {
        $this->waitTime = $waitTime;
        return $this;
    }

    public function getAbsences(): ?Absence
    {
        return $this->absences;
    }

    public function setAbsences(?Absence $absences): static
    {
        $this->absences = $absences;

        return $this;
    }

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }

    public function setSchedule(?Schedule $schedule): static
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroupCollection(): Collection
    {
        return $this->groupCollection;
    }

    public function addGroupCollection(Group $groupCollection): static
    {
        if (!$this->groupCollection->contains($groupCollection)) {
            $this->groupCollection->add($groupCollection);
        }

        return $this;
    }

    public function removeGroupCollection(Group $groupCollection): static
    {
        $this->groupCollection->removeElement($groupCollection);

        return $this;
    }
}
