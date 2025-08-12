<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;

class ProduitController extends AbstractController
{
    /**
     * Liste des produits
     */
    #[Route('/products', name: 'products_list')]
    public function list(Request $request, ProduitRepository $produitRepository)
    {
        $price = $request->query->get('prix'); // récupère le prix dans l'URL

        if ($price) {
            // filtre par prix exact
            $produits = $produitRepository->findBy(['prix' => $price]);
        } else {
            // sinon affiche tous les produits
            $produits = $produitRepository->findAll();
        }

        return $this->render('produit/list.html.twig', [
            'produits' => $produits,
            'priceFilter' => $price,
        ]);
    }

    /**
     * Page d'un produit
     */
    #[Route('/produit/{id}', name: 'product_show')]
    public function show(Produit $produit)
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit
        ]);
    }

    /**
     * Ajouter un produit au panier depuis la page produit
     */
    #[Route('/produit/{id}/ajouter', name: 'product_add', methods: ['POST'])]
    public function addToCart(
        Produit $produit,
        Request $request,
        SessionInterface $session
    ) {
        $taille = $request->request->get('taille');

        if (!$taille) {
            $this->addFlash('danger', 'Veuillez sélectionner une taille.');
            return $this->redirectToRoute('product_show', ['id' => $produit->getId()]);
        }

        $panier = $session->get('panier', []);

        // Vérifie si déjà présent avec cette taille
        $found = false;
        foreach ($panier as &$item) {
            if ($item['id'] === $produit->getId() && $item['taille'] === $taille) {
                $item['quantite']++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $panier[] = [
                'id' => $produit->getId(),
                'nom' => $produit->getNom(),
                'prix' => $produit->getPrix(),
                'image' => $produit->getImage(),
                'taille' => $taille,
                'quantite' => 1
            ];
        }

        $session->set('panier', $panier);

        $this->addFlash('success', 'Produit ajouté au panier.');
        return $this->redirectToRoute('cart_index');
    }
}
