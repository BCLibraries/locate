<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields = {"code"}, message = "Library code is already in use")
 * @ORM\Entity(repositoryClass="App\Repository\LibraryRepository")
 * @ORM\Table(
 *     name="library",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="code_unique",columns={"code"})}
 * )
 */
class Library
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Library alphanumeric code
     *
     * @Assert\Length(
     *     allowEmptyString = false,
     *     min = 3, minMessage = "Library code must be at least 3 characters long",
     *     max = 20, maxMessage = "Library code must be under 21 characters long"
     * )
     * @ORM\Column(type="string", length=24)
     */
    private $code;

    /**
     * Human-readable label
     *
     * @Assert\Length(allowEmptyString = false,
     *     max = 255,
     *     maxMessage= "Library labels must be under 256 characters long"
     * )
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /** @ORM\OneToMany(targetEntity="App\Entity\Map", mappedBy="library", orphanRemoval=true) */
    private $maps;

    public function __construct(string $code, string $label)
    {
        $this->maps = new ArrayCollection();
        $this->code = $code;
        $this->label = $label;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection|Map[]
     */
    public function getMaps(): Collection
    {
        return $this->maps;
    }

    public function addMap(Map $map): self
    {
        if (!$this->maps->contains($map)) {
            $this->maps[] = $map;
            $map->setLibrary($this);
        }

        return $this;
    }

    public function removeMap(Map $map): self
    {
        if ($this->maps->contains($map)) {
            $this->maps->removeElement($map);
            // set the owning side to null (unless already changed)
            if ($map->getLibrary() === $this) {
                $map->setLibrary(null);
            }
        }

        return $this;
    }
}
