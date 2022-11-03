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
    private ?string $trick_name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $trick_description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $trich_created = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $trick_modified = null;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    private ?user $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'trick_id')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $cadegory_id = null;

    #[ORM\OneToMany(mappedBy: 'trick_id', targetEntity: Media::class)]
    private Collection $media_type;

    public function __construct()
    {
        $this->media_type = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrickName(): ?string
    {
        return $this->trick_name;
    }

    public function setTrickName(string $trick_name): self
    {
        $this->trick_name = $trick_name;

        return $this;
    }

    public function getTrickDescription(): ?string
    {
        return $this->trick_description;
    }

    public function setTrickDescription(string $trick_description): self
    {
        $this->trick_description = $trick_description;

        return $this;
    }

    public function getTrichCreated(): ?\DateTimeInterface
    {
        return $this->trich_created;
    }

    public function setTrichCreated(\DateTimeInterface $trich_created): self
    {
        $this->trich_created = $trich_created;

        return $this;
    }

    public function getTrickModified(): ?\DateTimeInterface
    {
        return $this->trick_modified;
    }

    public function setTrickModified(?\DateTimeInterface $trick_modified): self
    {
        $this->trick_modified = $trick_modified;

        return $this;
    }

    public function getUserId(): ?user
    {
        return $this->user_id;
    }

    public function setUserId(?user $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getCadegoryId(): ?Category
    {
        return $this->cadegory_id;
    }

    public function setCadegoryId(?Category $cadegory_id): self
    {
        $this->cadegory_id = $cadegory_id;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMediaType(): Collection
    {
        return $this->media_type;
    }

    public function addMediaType(Media $mediaType): self
    {
        if (!$this->media_type->contains($mediaType)) {
            $this->media_type->add($mediaType);
            $mediaType->setTrickId($this);
        }

        return $this;
    }

    public function removeMediaType(Media $mediaType): self
    {
        if ($this->media_type->removeElement($mediaType)) {
            // set the owning side to null (unless already changed)
            if ($mediaType->getTrickId() === $this) {
                $mediaType->setTrickId(null);
            }
        }

        return $this;
    }
}
