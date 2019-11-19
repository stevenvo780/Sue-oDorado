<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use DateTime;

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
    private $nombre;

    /**
     * @ORM\Column(type="string", nullable=false, length=180)
     */
    private $telfijo;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $telmovil;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     */
    private $referido;

    /**
     * @ORM\Column(type="integer", length=255, nullable=false)
     */
    private $edad;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $ciudad;

    /**
     * @ORM\Column(type="string", length=180)
     */
    private $pais;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $monedasBitcoin;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $monedasMarketcoin;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $vecesRecividas;

    /**
     * @ORM\Column(type="date")
     */
    private $fechaCreacion;

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


    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

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

    public function getReferido(): ?User
    {
        return $this->referido;
    }

    public function setReferido(?User $referido): self
    {
        $this->referido = $referido;

        return $this;
    }

    public function getEdad(): ?int
    {
        return $this->edad;
    }

    public function setEdad(int $edad): self
    {
        $this->edad = $edad;

        return $this;
    }

    public function getMonedasBitcoin(): ?int
    {
        return $this->monedasBitcoin;
    }

    public function setMonedasBitcoin(int $monedasBitcoin): self
    {
        $this->monedasBitcoin = $monedasBitcoin;

        return $this;
    }

    public function getMonedasMarketcoin(): ?int
    {
        return $this->monedasMarketcoin;
    }

    public function setMonedasMarketcoin(int $monedasMarketcoin): self
    {
        $this->monedasMarketcoin = $monedasMarketcoin;

        return $this;
    }

    public function getPais(): ?string
    {
        return $this->pais;
    }

    public function setPais(string $pais): self
    {
        $this->pais = $pais;

        return $this;
    }

    public function getCiudad(): ?string
    {
        return $this->ciudad;
    }

    public function setCiudad(string $ciudad): self
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    public function getVecesRecividas(): ?int
    {
        return $this->vecesRecividas;
    }

    public function setVecesRecividas(int $vecesRecividas): self
    {
        $this->vecesRecividas = $vecesRecividas;

        return $this;
    }

    public function getFechaCreacion(): ?DateTime
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(DateTime $fechaCreacion): self
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->nombre;
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
