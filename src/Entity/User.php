<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *  class User with constrains
 */
#[ORM\Entity(repositoryClass:UserRepository::class)]
#[ORM\Table(name:'`user`')]
#[UniqueEntity('emailUser')]
#[UniqueEntity('username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column(type:'integer')]
private ?int $id = null;

#[Assert\Length(
    min : 3,
    max:50,
    minMessage:'Your first name must be at least {{ limit }} characters long',
    maxMessage:'Your first name cannot be longer than {{ limit }} characters',
)]
#[ORM\Column(length:255)]
private ?string $username = null;

#[ORM\Column(name : 'emailUser', type:'string', length:255, unique:true)]
#[Assert\Email(
    message:'The email {{ value }} is not a valid email.',
)]
protected ?string $emailUser = null;

#[Assert\Length(
    min : 8,
    minMessage:'Your password must be at least {{ limit }} characters long',
)]
#[ORM\Column(length:255)]
private ?string $passwordUser = null;

#[ORM\Column(type : 'json')]
private $roles = [];

// /**
//  * variable : issue with $roles....
//  *
//  * @var string|null
//  */
// #[ORM\Column(length: 10)]
// private ?string $roleUser = null;

#[ORM\Column(length:255)]
private ?string $pictureUserUrl = null;

/**
 * variable witche have nothing with DB
 *
 * @var [string]
 */
public $confirmPassword;

/**
 * variables for inferface
 *
 *
 */
public $eraseCredentials;

#[ORM\Column(length : 255)]
private ?string $token = null;

#[ORM\Column]
private ?bool $checkToken = null;

#[ORM\Column(type : Types::DATE_MUTABLE)]
private ?\DateTimeInterface $date = null;

#[ORM\OneToMany(mappedBy : 'user', targetEntity : Trick::class)]
private Collection $tricks;

#[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class)]
private Collection $comments;

/**
 * initialisation function
 */
function __construct()
    {
    $this->pictureUserUrl = "../assets/avatars/05.jpg";
    $this->token = "0";
    $this->checkToken = 0;
    $this->tricks = new ArrayCollection();
    $this->comments = new ArrayCollection();
}

function getId(): ?int
    {
    return $this->id;
}

function getEmailUser(): ?string
    {
    return $this->emailUser;
}

function setEmailUser(?string $emailUser): self
    {
    $this->emailUser = $emailUser;

    return $this;
}

function getUserName(): ?string
    {
    return $this->username;
}

function setUserName(string $username): self
    {
    $this->username = $username;

    return $this;
}

/**
 * @return string the hashed password for this user
 */
function getPasswordUser(): ?string
    {
    return $this->passwordUser;
}

/**
 * function getPassword necessary for UserInterface,
 *
 * @return string|null
 */
function getPassword(): ?string
    {
    return $this->passwordUser;
}
function setPassword(string $passwordUser): self
    {
    $this->passwordUser = $passwordUser;
    return $this;
}

function setPasswordUser(string $passwordUser): self
    {
    $this->passwordUser = $passwordUser;

    return $this;
}

function getRoleUser()
    {
    $this->getRoles();

    return $this;
}

function setRoleUser(string $roleUser): self
    {
    $this->roleUser = $roleUser;

    return $this;
}

function getPictureUserUrl(): ?string
    {
    return $this->pictureUserUrl;
}

function setPictureUserUrl(string $pictureUserUrl): self
    {
    $this->pictureUserUrl = $pictureUserUrl;

    return $this;
}

function getSalt()
    {
    // The bcrypt and argon2i algorithms don't require a separate salt.
    // You *may* need a real salt if you choose a different encoder.
    return null;
}

/**
 * The public representation of the user (e.g. a userName, an email address, etc.)
 *
 * @see UserInterface
 */
function getUserIdentifier(): string
    {
    return (string) $this->emailUser;
}

/**
 * @see UserInterface
 */

function setRoles(array $roles): self
    {
    $this->roles = $roles;

    return $this;
}

function getRoles(): array
{
    return $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = 'ROLE_USER';
    return array_unique($roles);
}

function eraseCredentials()
    {
}

function getToken(): ?string
    {
    return $this->token;
}

function setToken(string $token): self
    {
    $this->token = $token;

    return $this;
}

function isCheckToken(): ?bool
    {
    return $this->checkToken;
}

function setCheckToken(bool $checkToken): self
    {
    $this->checkToken = $checkToken;

    return $this;
}

function getDate(): ?\DateTimeInterface
{
    return $this->date;
}

function setDate(\DateTimeInterface$date): self
    {
    $this->date = $date;

    return $this;
}

/**
 * @return Collection<int, Trick>
 */
function getTricks(): Collection
    {
    return $this->tricks;
}

function addTrick(Trick $trick): self
    {
    if (!$this->tricks->contains($trick)) {
        $this->tricks->add($trick);
        $trick->setUserId($this);
    }

    return $this;
}

function removeTrick(Trick $trick): self
    {
    if ($this->tricks->removeElement($trick)) {
        // set the owning side to null (unless already changed)
        if ($trick->getUserId() === $this) {
            $trick->setUserId(null);
        }
    }

    return $this;
}

/**
 * @return Collection<int, Comment>
 */
public function getComments(): Collection
{
    return $this->comments;
}

public function addComment(Comment $comment): self
{
    if (!$this->comments->contains($comment)) {
        $this->comments->add($comment);
        $comment->setUserId($this);
    }

    return $this;
}

public function removeComment(Comment $comment): self
{
    if ($this->comments->removeElement($comment)) {
        // set the owning side to null (unless already changed)
        if ($comment->getUserId() === $this) {
            $comment->setUserId(null);
        }
    }

    return $this;
}
}
