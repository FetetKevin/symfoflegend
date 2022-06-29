<?php

namespace App\Entity;

use App\Repository\ChampLikeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChampLikeRepository::class)
 */
class ChampLike
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Champions::class, inversedBy="likes")
     */
    private $champion;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="likes")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChampion(): ?Champions
    {
        return $this->champion;
    }

    public function setChampion(?Champions $champion): self
    {
        $this->champion = $champion;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
