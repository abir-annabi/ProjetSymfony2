<?php

// src/Service/PanierService.php
namespace App\Service;

use App\Entity\Panier;
use App\Entity\PanierItem;
use App\Entity\User;
use App\Entity\Livres;
use Doctrine\ORM\EntityManagerInterface;

class PanierService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getPanier(User $user): Panier
    {
        $panier = $user->getPanier();
        
        if (!$panier) {
            $panier = new Panier();
            $panier->setUser($user);
            $this->entityManager->persist($panier);
            $this->entityManager->flush();
        }

        return $panier;
    }

    public function addItem(Panier $panier, Livres $livre, int $quantity = 1): void
    {
        foreach ($panier->getItems() as $item) {
            if ($item->getLivre()->getId() === $livre->getId()) {
                $item->setQuantity($item->getQuantity() + $quantity);
                $this->entityManager->flush();
                return;
            }
        }

        $item = new PanierItem();
        $item->setLivre($livre);
        $item->setQuantity($quantity);
        $item->setPanier($panier);
        
        $panier->addItem($item);
        
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    public function removeItem(PanierItem $item): void
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

    public function updateQuantity(PanierItem $item, int $quantity): void
    {
        $item->setQuantity($quantity);
        $this->entityManager->flush();
    }

    public function getTotalWithShipping(Panier $panier): float
{
    return $this->getTotal($panier) + 7.0; // 7DT pour la livraison
}

public function getShippingCost(): float
{
    return 7.0; // Frais de livraison fixes
}
    public function getTotal(Panier $panier): float
    {
        $total = 0;
        
        foreach ($panier->getItems() as $item) {
            $total += $item->getLivre()->getPrix() * $item->getQuantity();
        }
        
        return $total;
    }
}