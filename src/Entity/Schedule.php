<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
class Schedule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?int $weekDay = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $entrySlot = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\GreaterThan(
        propertyPath: 'entrySlot',
        message: 'La hora de salida no puede ser mayor a la hora de salida'
    )]
    private ?int $exitSlot = null;

    #[ORM\ManyToOne(inversedBy: 'schedules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Driver $driver = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeekDay(): ?string
    {
        return $this->weekDay;
    }

    public function setWeekDay(?string $weekDay): Schedule
    {
        $this->weekDay = $weekDay;
        return $this;
    }

    public function getEntrySlot(): ?int
    {
        return $this->entrySlot;
    }

    public function setEntrySlot(?int $entrySlot): Schedule
    {
        $this->entrySlot = $entrySlot;
        return $this;
    }

    public function getExitSlot(): ?int
    {
        return $this->exitSlot;
    }

    public function setExitSlot(?int $exitSlot): Schedule
    {
        $this->exitSlot = $exitSlot;
        return $this;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function setDriver(?Driver $driver): static
    {
        $this->driver = $driver;

        return $this;
    }
}
