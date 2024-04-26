<?php

namespace App\Entity;

use App\Repository\TimeTableRepository;
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
}
