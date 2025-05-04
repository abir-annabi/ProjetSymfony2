<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'panier', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'panier', targetEntity: PanierItem::class, orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, PanierItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(PanierItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setPanier($this);
        }

        return $this;
    }

    public function removeItem(PanierItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getPanier() === $this) {
                $item->setPanier(null);
            }
        }

        return $this;
    }

    public function getTotal(): float
    {
        $total = 0;
        
        foreach ($this->items as $item) {
            $total += $item->getLivre()->getPrix() * $item->getQuantity();
        }
        
        return $total;
    }
}