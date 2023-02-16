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
    private ?string $title;  //= null;

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

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\File(
        maxSize: '3M',
    )]
    private ?string $picture;
    private $slugger;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $modified_at = null; // make migration

    #[ORM\OneToMany(mappedBy: 'tricks', targetEntity: Pictures::class, cascade: ["all"], orphanRemoval: true)]
    private Collection $additionalTrick;

    #[ORM\OneToMany(mappedBy: 'relation', targetEntity: Comments::class, cascade: ["all"], orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $FirstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $LastName = null;

    public function __construct()
    {
        $this->setActive(1);
        $this->setCreatedAt(new \DateTimeImmutable("now"));
        $this->slugger = new AsciiSlugger();
        $this->setPicture("main-picture.jpg");
        $this->setDescription("X"); // not used for instance... so yet X
        $this->additionalTrick = new ArrayCollection();
        $this->comments = new ArrayCollection();
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
        $title = ucfirst($title);
        $this->title = $title;
        $titleMin = strtolower($title);
        $this->slugger = new AsciiSlugger();
        $this->setSlug($this->slugger->slug($titleMin));
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
    public function getAdditionalTrick(): Collection
    {
        return $this->additionalTrick;
    }

    public function addAdditionalTrick(Pictures $additionalTrick): self
    {
        if (!$this->additionalTrick->contains($additionalTrick)) {
            $this->additionalTrick->add($additionalTrick);
            $additionalTrick->setTricks($this);
        }

        return $this;
    }

    public function removeAdditionalTrick(Pictures $additionalTrick): self
    {
        if ($this->additionalTrick->removeElement($additionalTrick)) {
            // set the owning side to null (unless already changed)
            if ($additionalTrick->getTricks() === $this) {
                $additionalTrick->setTricks(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setRelation($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getRelation() === $this) {
                $comment->setRelation(null);
            }
        }

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(?string $FirstName): self
    {
        $this->FirstName = $FirstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->LastName;
    }

    public function setLastName(?string $LastName): self
    {
        $this->LastName = $LastName;

        return $this;
    }
}
