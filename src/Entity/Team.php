<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $team = null;

   
    #[ORM\OneToMany(mappedBy: 'guest', targetEntity: Game::class)]
    private Collection $gamesGuest;

    #[ORM\OneToMany(mappedBy: 'host', targetEntity: Game::class)]
    private Collection $gamesHost;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->gamesGuest = new ArrayCollection();
        $this->gamesHost = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(string $team): static
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGamesGuest(): Collection
    {
        return $this->gamesGuest;
    }

    public function addGamesGuest(Game $gamesGuest): static
    {
        if (!$this->gamesGuest->contains($gamesGuest)) {
            $this->gamesGuest->add($gamesGuest);
            $gamesGuest->setGuest($this);
        }

        return $this;
    }

    public function removeGamesGuest(Game $gamesGuest): static
    {
        if ($this->gamesGuest->removeElement($gamesGuest)) {
            // set the owning side to null (unless already changed)
            if ($gamesGuest->getGuest() === $this) {
                $gamesGuest->setGuest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGamesHost(): Collection
    {
        return $this->gamesHost;
    }

    public function addGamesHost(Game $gamesHost): static
    {
        if (!$this->gamesHost->contains($gamesHost)) {
            $this->gamesHost->add($gamesHost);
            $gamesHost->setHost($this);
        }

        return $this;
    }

    public function removeGamesHost(Game $gamesHost): static
    {
        if ($this->gamesHost->removeElement($gamesHost)) {
            // set the owning side to null (unless already changed)
            if ($gamesHost->getHost() === $this) {
                $gamesHost->setHost(null);
            }
        }

        return $this;
    }

    
}
