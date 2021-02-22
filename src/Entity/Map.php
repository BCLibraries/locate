<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MapRepository")
 * @ORM\Table(
 *     name="map",
 * )
 */
class Map
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="string", length=24) */
    private $code;

    /** @ORM\Column(type="string", length=255) */
    private $label;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Library", inversedBy="maps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $library;

    /** @ORM\OneToMany(targetEntity="App\Entity\Shelf", mappedBy="map") */
    private $shelves;

    /** @ORM\Embedded(class = "MapImage") */
    private $image;

    public function __construct(Library $library, string $code, string $label)
    {
        $this->library = $library;
        $this->code = $code;
        $this->label = $label;
        $this->shelves = new ArrayCollection();
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

    public function getLibrary(): ?Library
    {
        return $this->library;
    }

    public function setLibrary(?Library $library): self
    {
        $this->library = $library;

        return $this;
    }

    /**
     * @return Collection|Shelf[]
     */
    public function getShelves(): Collection
    {
        return $this->shelves;
    }

    public function addShelf(Shelf $shelf): self
    {
        if (!$this->shelves->contains($shelf)) {
            $this->shelves[] = $shelf;
            $shelf->setMap($this);
        }

        return $this;
    }

    public function removeShelf(Shelf $shelf): self
    {
        if ($this->shelves->contains($shelf)) {
            $this->shelves->removeElement($shelf);
            // set the owning side to null (unless already changed)
            if ($shelf->getMap() === $this) {
                $shelf->setMap(null);
            }
        }

        return $this;
    }

    public function getImage(): MapImage
    {
        return $this->image;
    }

    public function setImage(MapImage $image): void
    {
        $this->image = $image;
    }


}
