<?php
namespace App\Controller;

use App\Entity\Livres;
use App\Entity\PanierItem;
use App\Service\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(PanierService $panierService): Response
    {
        $panier = $panierService->getPanier($this->getUser());
        $total = $panierService->getTotal($panier);

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
            'total' => $total,
        ]);
    }

    #[Route('/panier/add/{id}', name: 'app_panier_add')]
public function add(int $id, EntityManagerInterface $entityManager, PanierService $panierService): Response
{
    $livre = $entityManager->getRepository(Livres::class)->find($id);
        if (!$livre) {
            throw $this->createNotFoundException('Livre non trouvé');
        }

        $panier = $panierService->getPanier($this->getUser());
        $panierService->addItem($panier, $livre);

        $this->addFlash('success', 'Livre ajouté au panier');

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/remove/{id}', name: 'app_panier_remove')]
    public function remove(PanierItem $item, PanierService $panierService): Response
    {
        $panierService->removeItem($item);

        $this->addFlash('success', 'Livre retiré du panier');

        return $this->redirectToRoute('app_panier');
    }

    #[Route('/panier/update/{id}', name: 'app_panier_update', methods: ['POST'])]
public function updateQuantity(PanierItem $item, Request $request, PanierService $panierService): Response
{
    $quantity = $request->request->getInt('quantity', 1);
    $panierService->updateQuantity($item, $quantity);
    
    // Retournez une réponse JSON pour une mise à jour AJAX
    if ($request->isXmlHttpRequest()) {
        $panier = $panierService->getPanier($this->getUser());
        return $this->json([
            'success' => true,
            'itemTotal' => $item->getTotal(),
            'subtotal' => $panierService->getTotal($panier),
            'totalWithShipping' => $panierService->getTotalWithShipping($panier),
        ]);
    }

    return $this->redirectToRoute('app_panier');
}
}