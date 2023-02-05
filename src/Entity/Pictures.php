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
    private ?string $picture = null;

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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    // public function removeAdditionalPicture(Pictures $Pictures): self
    // {
    //     if ($this->additionalTrick->removeElement($additionalTrick)) {
    //         // set the owning side to null (unless already changed)
    //         if ($additionalTrick->getTricks() === $this) {
    //             $additionalTrick->setTricks(null);
    //         }
    //     }

    //     return $this;
    // }

}
