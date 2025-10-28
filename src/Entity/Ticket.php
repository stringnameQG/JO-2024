<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $clefclient = null;

    #[ORM\Column(length: 255)]
    private ?string $clefticket = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Le nombre de place ne peut pas être vide ou inférieur à zéro')]
    private ?int $nombreDePlace = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClefclient(): ?string
    {
        return $this->clefclient;
    }

    public function setClefclient(string $clefclient): static
    {
        $this->clefclient = $clefclient;

        return $this;
    }

    public function getClefticket(): ?string
    {
        return $this->clefticket;
    }

    public function setClefticket(string $clefticket): static
    {
        $this->clefticket = $clefticket;

        return $this;
    }

    public function getNombreDePlace(): ?int
    {
        return $this->nombreDePlace;
    }

    public function setNombreDePlace(int $nombreDePlace): static
    {
        $this->nombreDePlace = $nombreDePlace;

        return $this;
    }
}
