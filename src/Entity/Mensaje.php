<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MensajeRepository")
 */
class Mensaje
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaEnvio;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaLectura;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $remitente;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $destinatario;

    /**
     * @ORM\Column(type="text")
     */
    private $mensaje;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $leido;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFechaEnvio(): ?\DateTimeInterface
    {
        return $this->fechaEnvio;
    }

    public function setFechaEnvio(\DateTimeInterface $fechaEnvio): self
    {
        $this->fechaEnvio = $fechaEnvio;

        return $this;
    }

    public function getFechaLectura(): ?\DateTimeInterface
    {
        return $this->fechaLectura;
    }

    public function setFechaLectura(?\DateTimeInterface $fechaLectura): self
    {
        $this->fechaLectura = $fechaLectura;

        return $this;
    }

    public function getRemitente(): ?User
    {
        return $this->remitente;
    }

    public function setRemitente(?User $remitente): self
    {
        $this->remitente = $remitente;

        return $this;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): self
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    public function getLeido(): ?bool
    {
        return $this->leido;
    }

    public function setLeido(?bool $leido): self
    {
        $this->leido = $leido;

        return $this;
    }

    public function getDestinatario(): ?User
    {
        return $this->destinatario;
    }

    public function setDestinatario(?User $destinatario): self
    {
        $this->destinatario = $destinatario;

        return $this;
    }
}
