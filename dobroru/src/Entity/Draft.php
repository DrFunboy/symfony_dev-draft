<?php

namespace App\Entity;

use App\Repository\DraftRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DraftRepository::class)]
class Draft
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $clientname = null;

    /**
     * @var Collection<int, Developer>
     */
    #[ORM\ManyToMany(targetEntity: Developer::class, mappedBy: 'iddraft')]
    private Collection $iddeveloper;

    #[ORM\Column(type: Types::SMALLINT, options: ['default' => 1])]
    private ?int $active = null;

    public function __construct()
    {
        $this->iddeveloper = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getClientname(): ?string
    {
        return $this->clientname;
    }

    public function setClientname(string $clientname): static
    {
        $this->clientname = $clientname;

        return $this;
    }

    /**
     * @return Collection<int, Developer>
     */
    public function getIddeveloper(): Collection
    {
        return $this->iddeveloper;
    }

    public function addIddeveloper(Developer $iddeveloper): static
    {
        if (!$this->iddeveloper->contains($iddeveloper)) {
            $this->iddeveloper->add($iddeveloper);
            $iddeveloper->addIddraft($this);
        }

        return $this;
    }

    public function removeIddeveloper(Developer $iddeveloper): static
    {
        if ($this->iddeveloper->removeElement($iddeveloper)) {
            $iddeveloper->removeIddraft($this);
        }

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): static
    {
        $this->active = $active;

        return $this;
    }
}
