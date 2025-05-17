<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $scoreHost = null;

    #[ORM\Column]
    private ?int $scoreGuest = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    private ?Stage $stage = null;

    #[ORM\ManyToOne(inversedBy: 'gamesGuest')]
    private ?Team $guest = null;

    #[ORM\ManyToOne(inversedBy: 'gamesHost')]
    private ?Team $host = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScoreHost(): ?int
    {
        return $this->scoreHost;
    }

    public function setScoreHost(int $scoreHost): static
    {
        $this->scoreHost = $scoreHost;

        return $this;
    }

    public function getScoreGuest(): ?int
    {
        return $this->scoreGuest;
    }

    public function setScoreGuest(int $scoreGuest): static
    {
        $this->scoreGuest = $scoreGuest;

        return $this;
    }

    public function getStage(): ?Stage
    {
        return $this->stage;
    }

    public function setStage(?Stage $stage): static
    {
        $this->stage = $stage;

        return $this;
    }

    public function getGameHost(): ?Team
    {
        return $this->gameHost;
    }

    public function setGameHost(?Team $gameHost): static
    {
        $this->gameHost = $gameHost;

        return $this;
    }

    public function getGameGuest(): ?Team
    {
        return $this->gameGuest;
    }

    public function setGameGuest(?Team $gameGuest): static
    {
        $this->gameGuest = $gameGuest;

        return $this;
    }

    public function getGuest(): ?Team
    {
        return $this->guest;
    }

    public function setGuest(?Team $guest): static
    {
        $this->guest = $guest;

        return $this;
    }

    public function getHost(): ?Team
    {
        return $this->host;
    }

    public function setHost(?Team $host): static
    {
        $this->host = $host;

        return $this;
    }
}
