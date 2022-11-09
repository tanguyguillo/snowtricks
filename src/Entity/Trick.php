<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $trickName = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $trickDescription = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $trichCreated = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $trickModified = null;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    private ?user $userId = null;

    #[ORM\ManyToOne(inversedBy: 'trickId')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $cadegoryId = null;

    #[ORM\OneToMany(mappedBy: 'trickId', targetEntity: Media::class)]
    private Collection $mediaType;

    public function __construct()
    {
        $this->mediaType = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrickName(): ?string
    {
        return $this->trickName;
    }

    public function setTrickName(string $trickName): self
    {
        $this->trickName = $trickName;

        return $this;
    }

    public function getTrickDescription(): ?string
    {
        return $this->trickDescription;
    }

    public function setTrickDescription(string $trickDescription): self
    {
        $this->trickDescription = $trickDescription;

        return $this;
    }

    public function getTrichCreated(): ?\DateTimeInterface
    {
        return $this->trichCreated;
    }

    public function setTrichCreated(\DateTimeInterface $trichCreated): self
    {
        $this->trichCreated = $trichCreated;

        return $this;
    }

    public function getTrickModified(): ?\DateTimeInterface
    {
        return $this->trickModified;
    }

    public function setTrickModified(?\DateTimeInterface $trickModified): self
    {
        $this->trickModified = $trickModified;

        return $this;
    }

    public function getUserId(): ?user
    {
        return $this->userId;
    }

    public function setUserId(?user $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getCadegoryId(): ?Category
    {
        return $this->cadegoryId;
    }

    public function setCadegoryId(?Category $cadegoryId): self
    {
        $this->cadegoryId = $cadegoryId;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMediaType(): Collection
    {
        return $this->mediaType;
    }

    public function addMediaType(Media $mediaType): self
    {
        if (!$this->mediaType->contains($mediaType)) {
            $this->mediaType->add($mediaType);
            $mediaType->setTrickId($this);
        }

        return $this;
    }

    public function removeMediaType(Media $mediaType): self
    {
        if ($this->mediaType->removeElement($mediaType)) {
            // set the owning side to null (unless already changed)
            if ($mediaType->getTrickId() === $this) {
                $mediaType->setTrickId(null);
            }
        }

        return $this;
    }
}
