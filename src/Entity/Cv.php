<?php

namespace App\Entity;

use App\Repository\CvRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CvRepository::class)]
class Cv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $experiences;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExperiences(): ?string
    {
        return $this->experiences;
    }

    public function setExperiences(string $experiences): self
    {
        $this->experiences = $experiences;

        return $this;
    }
}
