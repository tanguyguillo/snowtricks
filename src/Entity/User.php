<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]

class User //implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue] 
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private ?string $emailUser = null;


    #[ORM\Column(length: 255)]
    private ?string $passwordUser = null;


    #[ORM\Column(length: 10)]
    private ?string $roleUser = null;

    #[ORM\Column(length: 255)]
    private ?string $pictureUserUrl = null;



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

    public function getPasswordUser(): ?string
    {
        return $this->passwordUser;
    }

    public function setPasswordUser(string $passwordUser): self
    {
        $this->passwordUser = $passwordUser;

        return $this;
    }

    public function getRoleUser(): ?string
    {
        return $this->roleUser;
          // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
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

    // public function getRoles()
    // {
    //     return $this->roleUser;
    // }

    public function eraseCredentials()
    {
    }

}
