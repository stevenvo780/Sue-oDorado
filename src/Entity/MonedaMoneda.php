<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MonedaMonedaRepository")
 */
class MonedaMoneda
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
    private $monedaPropietario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Moneda")
     */
    private $monedaInvitado;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonedaPropietario(): ?Moneda
    {
        return $this->monedaPropietario;
    }

    public function setMonedaPropietario(?Moneda $monedaPropietario): self
    {
        $this->monedaPropietario = $monedaPropietario;

        return $this;
    }

    public function getMonedaInvitado(): ?Moneda
    {
        return $this->monedaInvitado;
    }

    public function setMonedaInvitado(?Moneda $monedaInvitado): self
    {
        $this->monedaInvitado = $monedaInvitado;

        return $this;
    }
}
