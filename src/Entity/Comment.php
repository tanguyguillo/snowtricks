<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $commentCreated = null;

    #[ORM\Column(length: 100)]
    private ?string $commentStatus = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentContent = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?trick $trickId = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?user $userId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentCreated(): ?\DateTimeInterface
    {
        return $this->commentCreated;
    }

    public function setCommentCreated(\DateTimeInterface $commentCreated): self
    {
        $this->commentCreated = $commentCreated;

        return $this;
    }

    public function getCommentStatus(): ?string
    {
        return $this->commentStatus;
    }

    public function setCommentStatus(string $commentStatus): self
    {
        $this->commentStatus = $commentStatus;

        return $this;
    }

    public function getCommentContent(): ?string
    {
        return $this->commentContent;
    }

    public function setCommentContent(?string $commentContent): self
    {
        $this->commentContent = $commentContent;

        return $this;
    }

    public function getTrickId(): ?trick
    {
        return $this->trickId;
    }

    public function setTrickId(trick $trickId): self
    {
        $this->trickId = $trickId;

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
}
