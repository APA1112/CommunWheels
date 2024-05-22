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

    #[ORM\Column]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dayDate = null;

    #[ORM\ManyToOne(inversedBy: 'nonSchoolDay')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $band;

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

    public function getBand(): ?Group
    {
        return $this->band;
    }

    public function setBand(?Group $band): static
    {
        $this->band = $band;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): NonSchoolDay
    {
        $this->description = $description;
        return $this;
    }

}
