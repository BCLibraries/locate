<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Embeddable */
class MapImage
{
    /** @ORM\Column(type="string", length=255) */
    private $original_filename;

    /** @ORM\Column(type="string", length=255) */
    private $filename;

    /** @ORM\Column(type="smallint", options={"unsigned":true}) */
    private $height;

    /** @ORM\Column(type="smallint", options={"unsigned":true}) */
    private $width;

    public function getOriginalFilename(): string
    {
        return $this->original_filename;
    }

    public function setOriginalFilename(string $original_filename): self
    {
        $this->original_filename = $original_filename;
        return $this;
    }

    public function getFilename():string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height):self
    {
        $this->height = $height;
        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;
        return $this;
    }
}