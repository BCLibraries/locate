<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShelfRepository")
 * @ORM\Table(
 *     name="shelf",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="code_unique",columns={"code"})},
 *     indexes={@ORM\Index(name="callno_idx", columns={"start_sort_call_number", "end_sort_call_number"})}
 * )
 */
class Shelf
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="string", length=255) */
    private $code;

    /** @ORM\Column(type="string", length=255) */
    private $start_call_number;

    /** @ORM\Column(type="string", length=255) */
    private $end_call_number;

    /** @ORM\Column(type="string", length=255) */
    private $start_sort_call_number;

    /** @ORM\Column(type="string", length=255) */
    private $end_sort_call_number;

    /** @ORM\Column(type="smallint", options={"unsigned":true}) */
    private $x;

    /** @ORM\Column(type="smallint", options={"unsigned":true}) */
    private $y;

    /** @ORM\ManyToOne(targetEntity="App\Entity\Map", inversedBy="shelves") */
    private $map;

    /** @ORM\Column(type="smallint", options={"unsigned":true}) */
    private $orientation;

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

    public function getStartCallNumber(): ?string
    {
        return $this->start_call_number;
    }

    public function setStartCallNumber(string $start_call_number): self
    {
        $this->start_call_number = $start_call_number;

        return $this;
    }

    public function getEndCallNumber(): ?string
    {
        return $this->end_call_number;
    }

    public function setEndCallNumber(string $end_call_number): self
    {
        $this->end_call_number = $end_call_number;

        return $this;
    }

    public function getStartSortCallNumber(): ?string
    {
        return $this->start_sort_call_number;
    }

    public function setStartSortCallNumber(string $start_sort_call_number): self
    {
        $this->start_sort_call_number = $start_sort_call_number;

        return $this;
    }

    public function getEndSortCallNumber(): ?string
    {
        return $this->end_sort_call_number;
    }

    public function setEndSortCallNumber(string $end_sort_call_number): self
    {
        $this->end_sort_call_number = $end_sort_call_number;

        return $this;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(int $x): self
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(int $y): self
    {
        $this->y = $y;

        return $this;
    }

    public function getMap(): ?Map
    {
        return $this->map;
    }

    public function setMap(?Map $map): self
    {
        $this->map = $map;

        return $this;
    }

    public function getOrientation(): int
    {
        return $this->orientation;
    }

    public function setOrientation(in $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }
}
