<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use App\Repository\TricksRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TricksRepository::class)]
#[UniqueEntity(fields: ['title'], message: "There's already a trick name like this one!")]
class Tricks
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Length(
        min: 4,
        max: 50,
        minMessage: 'Your trick title must be at least {{ limit }} characters long',
        maxMessage: 'Your trick title cannot be longer than {{ limit }} characters',
    )]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?bool $active = null;

    // relations
    #[ORM\ManyToOne(inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    private $slugger;

    #[ORM\Column(length: 255)]
    #[Assert\File(
        maxSize: '3M',
    )]
    private ?string $picture = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $modified_at = null;

    #[ORM\OneToMany(mappedBy: 'tricks', targetEntity: Pictures::class, orphanRemoval: true)]
    private Collection $additionnalTrick;

    public function __construct()
    {
        $this->setActive(1);
        $this->setCreatedAt(new \DateTimeImmutable("now"));
        $this->slugger = new AsciiSlugger();

        $this->setPicture("main-picture.jpg");
        $this->setDescription("O");
        $this->additionnalTrick = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $titleMin = strtolower($title);
        $this->setSlug($this->slugger->slug($titleMin));

        $title = ucfirst($title);
        $this->title = $title;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
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

    public function getModifiedAt(): ?\DateTimeImmutable
    {
        return $this->modified_at;
    }

    public function setModifiedAt(?\DateTimeImmutable $modified_at): self
    {
        $this->modified_at = $modified_at;

        return $this;
    }

    /**
     * @return Collection<int, Pictures>
     */
    public function getAdditionnalTrick(): Collection
    {
        return $this->additionnalTrick;
    }

    public function addAdditionnalTrick(Pictures $additionnalTrick): self
    {
        if (!$this->additionnalTrick->contains($additionnalTrick)) {
            $this->additionnalTrick->add($additionnalTrick);
            $additionnalTrick->setTricks($this);
        }

        return $this;
    }

    public function removeAdditionnalTrick(Pictures $additionnalTrick): self
    {
        if ($this->additionnalTrick->removeElement($additionnalTrick)) {
            // set the owning side to null (unless already changed)
            if ($additionnalTrick->getTricks() === $this) {
                $additionnalTrick->setTricks(null);
            }
        }

        return $this;
    }
}
