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

#[ORM\OneToMany(mappedBy : 'cadegoryId', targetEntity:trick::class)]
private Collection $trickId;

#[ORM\Column(length:45)]
private  ?string $categotyName = null;

public function __construct()
    {
    $this->trickId = new ArrayCollection();
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
    return $this->trickId;
}

public function addTrickId(trick $trickId): self
    {
    if (!$this->trickId->contains($trickId)) {
        $this->trickId->add($trickId);
        $trickId->setCadegoryId($this);
    }
    return $this;
}

public function removeTrickId(trick $trickId): self
    {
    if ($this->trickId->removeElement($trickId)) {
        // set the owning side to null (unless already changed)
        if ($trickId->getCadegoryId() === $this) {
            $trickId->setCadegoryId(null);
        }
    }

    return $this;
}

public function getCategotyName(): ?string
    {
    return $this->categotyName;
}

public function setCategotyName(string $categotyName): self
    {
    $this->categotyName = $categotyName;

    return $this;
}
}
