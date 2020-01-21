<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MonedaApoyoRepository")
 */
class MonedaApoyo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Moneda")
     */
    private $moneda;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Moneda")
     */
    private $monedaDApoyo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonedaDApoyo(): ?Moneda
    {
        return $this->monedaDApoyo;
    }

    public function setMonedaDApoyo(?Moneda $monedaDApoyo): self
    {
        $this->monedaDApoyo = $monedaDApoyo;

        return $this;
    }

    public function getMoneda(): ?Moneda
    {
        return $this->moneda;
    }

    public function setMoneda(?Moneda $moneda): self
    {
        $this->moneda = $moneda;

        return $this;
    }
}
