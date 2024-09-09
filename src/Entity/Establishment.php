<?php
namespace App\Entity;

use App\Repository\EstablishmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EstablishmentRepository::class)]
class Establishment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'establishments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $company = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column]
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    #[ORM\Column]
    private string $address;

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    #[ORM\Column]
    private string $city;

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    #[ORM\Column]
    private string $postalCode;

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    #[ORM\Column]
    private string $country;

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    #[ORM\Column]
    private string $phone;

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    #[ORM\Column]
    private string $email;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    #[ORM\Column]
    private string $website;

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function setWebsite(string $website): void
    {
        $this->website = $website;
    }

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'establishments')]
    #[ORM\JoinColumn(nullable: false)]

    public function getCompany(): ?User
    {
        return $this->company;
    }

    public function setCompany(?User $company): self
    {
        $this->company = $company;

        return $this;
    }

    #[ORM\Column]
    private string $siret;

    public function getSiret(): string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): void
    {
        $this->siret = $siret;
    }
}