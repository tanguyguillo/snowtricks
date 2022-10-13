<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 *  implements //UserInterface, PasswordAuthenticatedUserInterface
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]


/**
 * Undocumented class
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue] 
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Your first name must be at least {{ limit }} characters long',
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $emailUser = null;

    #[Assert\Length(
        min: 8,
        minMessage: 'Your password must be at least {{ limit }} characters long',
    )]
    #[ORM\Column(length: 255)]
    private ?string $passwordUser = null;

    /**
     * variable : issue with $roles....
     *
     * @var string|null
     */
    #[ORM\Column(length: 10)]
    private ?string $roleUser = null;

    #[ORM\Column(length: 255)]
    private ?string $pictureUserUrl = null;

    /**
    * variable witche have nothing with DB
    *
    * @var [string]
    */
    public $confirm_password;

    /**
     * variables for 
     *
     * @var array
     */
    private $roles = [];
    public $eraseCredentials;

    /**
     * initialisation function
     */
    function __construct() {

        $this->roles[] = 'ROLE_USER';
        $this->pictureUserUrl = "../assets/img/snowboard-background.png";
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailUser(): ?string
    {
        return $this->emailUser;
    }

    public function setEmailUser(?string $emailUser): self
    {
        $this->emailUser = $emailUser;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string the hashed password for this user
     */
    public function getPasswordUser(): ?string
    {
        return $this->passwordUser;
    }

    /**
     * function getPassword necessary for UserInterface,
     *
     * @return string|null
     */
    public function  getPassword(): ?string
    {
        return $this->passwordUser;
    }
    public function  setPassword(string $passwordUser): self
    {
        $this->passwordUser = $passwordUser;
        return $this;
    }


    public function setPasswordUser(string $passwordUser): self
    {
        $this->passwordUser = $passwordUser;

        return $this;
    }

    public function getRoleUser()
    {
        $this->getRoles();

        return $this;
    }

    public function setRoleUser(string $roleUser): self
    {
        $this->roleUser = $roleUser;

        return $this;
    }

    public function getPictureUserUrl(): ?string
    {
        return $this->pictureUserUrl;
    }

    public function setPictureUserUrl(string $pictureUserUrl): self
    {
        $this->pictureUserUrl = $pictureUserUrl;

        return $this;
    }

    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
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

    public function setRoles()
    {
        return $this->roleUser;
    }

    public function getRoles(): array
    {
        return $this->roleUser;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function eraseCredentials()
    {
    }
}
