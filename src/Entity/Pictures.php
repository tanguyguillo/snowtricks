<?php

namespace App\Entity;

use App\Repository\PicturesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PicturesRepository::class)]
class Pictures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'additionnalTrick')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tricks $tricks = null;

    #[ORM\Column(length: 255)]
    private ?string $picure = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTricks(): ?Tricks
    {
        return $this->tricks;
    }

    public function setTricks(?Tricks $tricks): self
    {
        $this->tricks = $tricks;

        return $this;
    }

    public function getPicure(): ?string
    {
        return $this->picure;
    }

    public function setPicure(string $picure): self
    {
        $this->picure = $picure;

        return $this;
    }
}
