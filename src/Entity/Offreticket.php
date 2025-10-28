<?php

namespace App\Entity;

use App\Repository\OffreticketRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OffreticketRepository::class)]
class Offreticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomOffre = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\Positive(message: 'Le prix ne peut pas être vide ou inférieur à zéro')]
    private ?string $prix = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $compteurOffreVendue = 0;

    #[ORM\Column]
    private ?int $nombrePlace = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getNomOffre(): ?string
    {
        return $this->nomOffre;
    }

    public function setNomOffre(string $nomOffre): static
    {
        $this->nomOffre = $nomOffre;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCompteurOffreVendue(): ?int
    {
        return $this->compteurOffreVendue;
    }

    public function setCompteurOffreVendue(?int $compteurOffreVendue): static
    {
        $this->compteurOffreVendue = $compteurOffreVendue;

        return $this;
    }

    public function getNombrePlace(): ?int
    {
        return $this->nombrePlace;
    }

    public function setNombrePlace(int $nombrePlace): static
    {
        $this->nombrePlace = $nombrePlace;

        return $this;
    }
}