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
    private ?\DateTimeInterface $comment_created = null;

    #[ORM\Column(length: 100)]
    private ?string $comment_status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment_content = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?trick $trick_id = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?user $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentCreated(): ?\DateTimeInterface
    {
        return $this->comment_created;
    }

    public function setCommentCreated(\DateTimeInterface $comment_created): self
    {
        $this->comment_created = $comment_created;

        return $this;
    }

    public function getCommentStatus(): ?string
    {
        return $this->comment_status;
    }

    public function setCommentStatus(string $comment_status): self
    {
        $this->comment_status = $comment_status;

        return $this;
    }

    public function getCommentContent(): ?string
    {
        return $this->comment_content;
    }

    public function setCommentContent(?string $comment_content): self
    {
        $this->comment_content = $comment_content;

        return $this;
    }

    public function getTrickId(): ?trick
    {
        return $this->trick_id;
    }

    public function setTrickId(trick $trick_id): self
    {
        $this->trick_id = $trick_id;

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
}
