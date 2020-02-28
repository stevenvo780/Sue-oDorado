<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MonedaRepository")
 */
class Moneda
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $dueño;

    /**
     * @ORM\Column(type="integer")
     */
    private $vecesRecibidas;

    /**
     * @ORM\Column(type="integer")
     */
    private $rango = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $posicion = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $dono = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDueño(): ?User
    {
        return $this->dueño;
    }

    public function setDueño(?User $dueño): self
    {
        $this->dueño = $dueño;

        return $this;
    }

    public function getVecesRecibidas(): ?int
    {
        return $this->vecesRecibidas;
    }

    public function setVecesRecibidas(int $vecesRecibidas): self
    {
        $this->vecesRecibidas = $vecesRecibidas;

        return $this;
    }

    public function getRango(): ?int
    {
        return $this->rango;
    }

    public function setRango(?int $rango): self
    {
        $this->rango = $rango;

        return $this;
    }

    public function getDono(): ?int
    {
        return $this->dono;
    }

    public function setDono(?int $dono): self
    {
        $this->dono = $dono;

        return $this;
    }

    public function getPosicion(): ?int
    {
        return $this->posicion;
    }

    public function setPosicion(int $posicion): self
    {
        $this->posicion = $posicion;

        return $this;
    }
}
