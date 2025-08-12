<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProduitRepository $repo)
    {
        $featuredProducts = $repo->findBy(['featured' => true], null, 3);

        return $this->render('home/index.html.twig', [
            'featuredProducts' => $featuredProducts
        ]);
    }
}
