<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventosRepository")
 */
class Eventos
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="date")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descripcion;

    /**
     * @ORM\Column(type="time")
     */
    private $duracionInicio;

    /**
     * @ORM\Column(type="time")
     */
    private $duracionFin;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getDuracionInicio(): ?\DateTimeInterface
    {
        return $this->duracionInicio;
    }

    public function setDuracionInicio(\DateTimeInterface $duracionInicio): self
    {
        $this->duracionInicio = $duracionInicio;

        return $this;
    }

    public function getDuracionFin(): ?\DateTimeInterface
    {
        return $this->duracionFin;
    }

    public function setDuracionFin(\DateTimeInterface $duracionFin): self
    {
        $this->duracionFin = $duracionFin;

        return $this;
    }
}
