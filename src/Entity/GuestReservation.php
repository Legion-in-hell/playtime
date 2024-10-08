<?php

namespace App\Entity;

use App\Repository\GuestReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GuestReservationRepository::class)]
class GuestReservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $service;

    #[ORM\Column(type: 'datetime')]
    private $dateTime;

    #[ORM\ManyToOne(targetEntity: SportCompany::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $sportCompany;

    #[ORM\Column(type: 'string', length: 255)]
    private $clientFirstName;

    #[ORM\Column(type: 'string', length: 255)]
    private $clientLastName;

    #[ORM\Column(type: 'string', length: 255)]
    private $clientEmail;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $clientPhone;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;
        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    public function getSportCompany(): ?SportCompany
    {
        return $this->sportCompany;
    }

    public function setSportCompany(?SportCompany $sportCompany): self
    {
        $this->sportCompany = $sportCompany;
        return $this;
    }

    public function getClientFirstName(): ?string
    {
        return $this->clientFirstName;
    }

    public function setClientFirstName(string $clientFirstName): self
    {
        $this->clientFirstName = $clientFirstName;
        return $this;
    }

    public function getClientLastName(): ?string
    {
        return $this->clientLastName;
    }

    public function setClientLastName(string $clientLastName): self
    {
        $this->clientLastName = $clientLastName;
        return $this;
    }

    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }

    public function setClientEmail(string $clientEmail): self
    {
        $this->clientEmail = $clientEmail;
        return $this;
    }

    public function getClientPhone(): ?string
    {
        return $this->clientPhone;
    }

    public function setClientPhone(?string $clientPhone): self
    {
        $this->clientPhone = $clientPhone;
        return $this;
    }
}