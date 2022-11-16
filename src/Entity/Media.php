<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1000)]
    private ?string $mediaUrl = null;

    #[ORM\Column(length: 45)]
    private ?string $mediaName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $mediaCreated = null;

    #[ORM\ManyToOne(inversedBy: 'mediaType')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trick $trickId = null;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMediaUrl(): ?string
    {
        return $this->mediaUrl;
    }

    public function setMediaUrl(string $mediaUrl): self
    {
        $this->mediaUrl = $mediaUrl;

        return $this;
    }

    public function getMediaName(): ?string
    {
        return $this->mediaName;
    }

    public function setMediaName(string $mediaName): self
    {
        $this->mediaName = $mediaName;

        return $this;
    }

    public function getMediaCreated(): ?\DateTimeInterface
    {
        return $this->mediaCreated;
    }

    public function setMediaCreated(\DateTimeInterface $mediaCreated): self
    {
        $this->mediaCreated = $mediaCreated;

        return $this;
    }

    public function getTrickId(): ?trick
    {
        return $this->trickId;
    }

    public function setTrickId(?trick $trickId): self
    {
        $this->trickId = $trickId;

        return $this;
    }
}
