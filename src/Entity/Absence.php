<?php

namespace App\Entity;

use App\Repository\AbsenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbsenceRepository::class)]
class Absence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $absenceDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbsenceDate(): ?\DateTimeInterface
    {
        return $this->absenceDate;
    }

    public function setAbsenceDate(?\DateTimeInterface $absenceDate): Absence
    {
        $this->absenceDate = $absenceDate;
        return $this;
    }

}
