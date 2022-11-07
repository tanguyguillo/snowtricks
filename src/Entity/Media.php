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
    private ?string $media_url = null;

    #[ORM\Column(length: 45)]
    private ?string $media_name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $media_created = null;

    #[ORM\ManyToOne(inversedBy: 'media_type')]
    #[ORM\JoinColumn(nullable: false)]
    private ?trick $trick_id = null;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMediaUrl(): ?string
    {
        return $this->media_url;
    }

    public function setMediaUrl(string $media_url): self
    {
        $this->media_url = $media_url;

        return $this;
    }

    public function getMediaName(): ?string
    {
        return $this->media_name;
    }

    public function setMediaName(string $media_name): self
    {
        $this->media_name = $media_name;

        return $this;
    }

    public function getMediaCreated(): ?\DateTimeInterface
    {
        return $this->media_created;
    }

    public function setMediaCreated(\DateTimeInterface $media_created): self
    {
        $this->media_created = $media_created;

        return $this;
    }

    public function getTrickId(): ?trick
    {
        return $this->trick_id;
    }

    public function setTrickId(?trick $trick_id): self
    {
        $this->trick_id = $trick_id;

        return $this;
    }
}
