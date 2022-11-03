<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass:CategoryRepository::class)]
class Category
{
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column]
private ?int $id = null;

#[ORM\OneToMany(mappedBy : 'cadegory_id', targetEntity:trick::class)]
private Collection $trick_id;

#[ORM\Column(length:45)]
private  ?string $categoty_name = null;

public function __construct()
    {
    $this->trick_id = new ArrayCollection();
}

public function getId() : ?int
    {
    return $this->id;
}

/**
 * @return Collection<int, trick>
 */
public function getTrickId(): Collection
    {
    return $this->trick_id;
}

public function addTrickId(trick $trickId): self
    {
    if (!$this->trick_id->contains($trickId)) {
        $this->trick_id->add($trickId);
        $trickId->setCadegoryId($this);
    }
    return $this;
}

public function removeTrickId(trick $trickId): self
    {
    if ($this->trick_id->removeElement($trickId)) {
        // set the owning side to null (unless already changed)
        if ($trickId->getCadegoryId() === $this) {
            $trickId->setCadegoryId(null);
        }
    }

    return $this;
}

public function getCategotyName(): ?string
    {
    return $this->categoty_name;
}

public function setCategotyName(string $categoty_name): self
    {
    $this->categoty_name = $categoty_name;

    return $this;
}
}
