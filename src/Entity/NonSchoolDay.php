<?php

namespace App\Entity;

use App\Repository\NonSchoolDayRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NonSchoolDayRepository::class)]
class NonSchoolDay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dayDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayDate(): ?\DateTimeInterface
    {
        return $this->dayDate;
    }

    public function setDayDate(\DateTimeInterface $dayDate): static
    {
        $this->dayDate = $dayDate;

        return $this;
    }
}
