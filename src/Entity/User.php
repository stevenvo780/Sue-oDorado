<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", nullable=false, length=180)
     */
    private $telfijo;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $telmovil;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $referido;

    /**
     * @ORM\Column(type="integer", length=255, nullable=false)
     */
    private $edad;

    /**
     * @ORM\Column(type="integer", length=255, nullable=false)
     */
    private $nivel;

    /**
     * @ORM\Column(type="integer", length=255, nullable=false)
     */
    private $monedas;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     */
    private $plainPassword;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getTelfijo(): ?string
    {
        return $this->telfijo;
    }

    public function setTelfijo(string $telfijo): self
    {
        $this->telfijo = $telfijo;

        return $this;
    }

    public function getTelmovil(): ?string
    {
        return $this->telmovil;
    }

    public function setTelmovil(string $telmovil): self
    {
        $this->telmovil = $telmovil;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getReferido(): ?string
    {
        return $this->referido;
    }

    public function setReferido(string $referido): self
    {
        $this->referido = $referido;

        return $this;
    }

    public function getEdad(): ?string
    {
        return $this->edad;
    }

    public function setEdad(string $edad): self
    {
        $this->edad = $edad;

        return $this;
    }

    public function getNivel(): ?string
    {
        return $this->nivel;
    }

    public function setNivel(string $nivel): self
    {
        $this->nivel = $nivel;

        return $this;
    }

    public function getMonedas(): ?string
    {
        return $this->monedas;
    }

    public function setMonedas(string $monedas): self
    {
        $this->monedas = $monedas;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
