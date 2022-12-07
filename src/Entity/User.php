<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[Assert\Length(
    //     min: 3,
    //     max: 50,
    //     minMessage: 'Your first name must be at least {{ limit }} characters long',
    //     maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    // )]
    // #[ORM\Column(180)] // , unique: true
    // private ?string $username = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type : 'json')]
    private $roles = [];

    // #[ORM\Column(length: 255)]
    // private ?string $avatar = "";

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    /**
     * initialisation function
     */
    function __construct()
    {
        //$this->getRoles();
        // $this->token = "0";
        // $this->checkToken = 0;
        // $this->tricks = new ArrayCollection();
        // $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    // function getUserName(): ?string
    // {
    //     return $this->username;
    // }

    // function setUserName(string $username): self
    // {
    //     $this->username = $username;

    //     return $this;
    // }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] =  'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    // function getAvatar(): ?string
    // {
    //     return $this->avatar;
    // }

    // function setAvatar(string $avatar): self
    // {
    //     $this->avatar = $avatar;

    //     return $this;
    // }
}
