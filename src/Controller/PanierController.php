<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PanierController extends AbstractController
{
    // Afficher le panier
    #[Route('/panier', name: 'cart_index')]
    public function index(SessionInterface $session)
    {
        $cart = $session->get('panier', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += ($item['prix'] ?? 0) * ($item['quantite'] ?? 1);
        }

        return $this->render('panier/index.html.twig', [
            'panierItems' => $cart,
            'total' => $total
        ]);
    }

    // Ajouter un produit au panier (POST)
    #[Route('/panier/ajouter/{id}', name: 'cart_add', methods:['POST'])]
    public function add(int $id, Request $request, SessionInterface $session, ProduitRepository $repo)
    {
        $taille = $request->request->get('taille');

        if (!$taille) {
            $this->addFlash('error', 'Veuillez sélectionner une taille.');
            return $this->redirectToRoute('product_show', ['id' => $id]);
        }

        $produit = $repo->find($id);
        if (!$produit) {
            $this->addFlash('error', 'Produit introuvable.');
            return $this->redirectToRoute('products_list');
        }

        $cart = $session->get('panier', []);
        $found = false;

        // la taille dans le panier doit etre sune string et pas un tableau
        foreach ($cart as &$item) {
            // Si la taille dans le panier est un tableau on prend le premier élément
            if (is_array($item['taille'])) {
                $item['taille'] = reset($item['taille']);
                if (!isset($item['quantite'])) {
                $item['quantite'] = 1;
            }
            }

            // Si produit+taille identiques, on incrémente la quantité
            if ($item['id'] === $id && $item['taille'] === $taille) {
                $item['quantite']++;
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $cart[] = [
                'id' => $produit->getId(),
                'nom' => $produit->getNom(),
                'prix' => $produit->getPrix(),
                'image' => $produit->getImage(),
                'taille' => $taille,  // taille est une string ici
                'quantite' => 1,
            ];
        }

        $session->set('panier', $cart);
        $this->addFlash('success', 'Produit ajouté au panier.');
        return $this->redirectToRoute('cart_index');
    }

    // Mettre à jour la quantité d'un item (POST)
    #[Route('/panier/update/{index}', name: 'cart_update', methods:['POST'])]
    public function update(int $index, Request $request, SessionInterface $session)
    {
        $qty = (int)$request->request->get('quantite', 1);
        if ($qty < 1) {
            $qty = 1;
        }

        $cart = $session->get('panier', []);
        if (isset($cart[$index])) {
            $cart[$index]['quantite'] = $qty;
            $session->set('panier', $cart);
            $this->addFlash('success','Quantité mise à jour.');
        }

        return $this->redirectToRoute('cart_index');
    }

    // Supprimer un item (GET ou POST)
    #[Route('/panier/supprimer/{index}', name: 'cart_remove')]
    public function remove(int $index, SessionInterface $session)
    {
        $cart = $session->get('panier', []);
        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart); // réindexer
            $session->set('panier', $cart);
            $this->addFlash('success','Article supprimé.');
        }
        return $this->redirectToRoute('cart_index');
    }

    // Checkout Stripe
    #[Route('/panier/checkout', name: 'cart_checkout', methods:['POST'])]
    public function checkout(SessionInterface $session, UrlGeneratorInterface $urlGenerator)
    {

        $cart = $session->get('panier', []);
        if (empty($cart)) {
            $this->addFlash('error','Votre panier est vide.');
            return $this->redirectToRoute('cart_index');
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY'] ?? getenv('STRIPE_SECRET_KEY'));

        $lineItems = [];
        foreach ($cart as $item) {
            $unitAmount = (int)round(($item['prix'] ?? 0) * 100);
            $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => $item['nom'] . ' - ' . $item['taille'],
                ],
                'unit_amount' => $unitAmount,
            ],
            'quantity' => max(1, (int) ($item['quantite'] ?? 1)),
        ];
        }

        $checkoutSession = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $urlGenerator->generate('cart_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $urlGenerator->generate('cart_index', [], UrlGeneratorInterface::ABSOLUTE_URL),

        ]);

        return new RedirectResponse($checkoutSession->url, 303);
    }

    // Page succès (vide le panier)
    #[Route('/panier/success', name: 'cart_success')]
    public function success(SessionInterface $session)
    {
        // Ici tu peux enregistrer la commande en BDD si tu veux
        $session->remove('panier');
        $this->addFlash('success','Paiement réussi. Merci pour votre commande !');

        return $this->redirectToRoute('products_list');
    }
}
