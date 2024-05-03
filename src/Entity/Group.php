<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $origin = null;

    #[ORM\Column(length: 255)]
    private ?string $destination = null;

    #[ORM\ManyToMany(targetEntity: Driver::class, inversedBy: 'groupCollection')]
    private Collection $drivers;

    #[ORM\OneToMany(mappedBy: 'band', targetEntity: NonSchoolDay::class, orphanRemoval: true)]
    private Collection $nonSchoolDay;

    #[ORM\OneToMany(mappedBy: 'band', targetEntity: TimeTable::class, orphanRemoval: true)]
    private Collection $timeTables;

    public function __construct()
    {
        $this->drivers = new ArrayCollection();
        $this->nonSchoolDay = new ArrayCollection();
        $this->timeTables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Group
    {
        $this->name = $name;
        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(?string $origin): Group
    {
        $this->origin = $origin;
        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): Group
    {
        $this->destination = $destination;
        return $this;
    }

    /**
     * @return Collection<int, Driver>
     */
    public function getDrivers(): Collection
    {
        return $this->drivers;
    }

    public function addDriver(Driver $driver): static
    {
        if (!$this->drivers->contains($driver)) {
            $this->drivers->add($driver);
            $driver->addGroupCollection($this);
        }

        return $this;
    }

    public function removeDriver(Driver $driver): static
    {
        if ($this->drivers->removeElement($driver)) {
            $driver->removeGroupCollection($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, NonSchoolDay>
     */
    public function getNonSchoolDay(): Collection
    {
        return $this->nonSchoolDay;
    }

    public function addNonSchoolDay(NonSchoolDay $nonSchoolDay): static
    {
        if (!$this->nonSchoolDay->contains($nonSchoolDay)) {
            $this->nonSchoolDay->add($nonSchoolDay);
            $nonSchoolDay->setBand($this);
        }

        return $this;
    }

    public function removeNonSchoolDay(NonSchoolDay $nonSchoolDay): static
    {
        if ($this->nonSchoolDay->removeElement($nonSchoolDay)) {
            // set the owning side to null (unless already changed)
            if ($nonSchoolDay->getBand() === $this) {
                $nonSchoolDay->setBand(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TimeTable>
     */
    public function getTimeTables(): Collection
    {
        return $this->timeTables;
    }

    public function addTimeTable(TimeTable $timeTable): static
    {
        if (!$this->timeTables->contains($timeTable)) {
            $this->timeTables->add($timeTable);
            $timeTable->setBand($this);
        }

        return $this;
    }

    public function removeTimeTable(TimeTable $timeTable): static
    {
        if ($this->timeTables->removeElement($timeTable)) {
            // set the owning side to null (unless already changed)
            if ($timeTable->getBand() === $this) {
                $timeTable->setBand(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }


}
