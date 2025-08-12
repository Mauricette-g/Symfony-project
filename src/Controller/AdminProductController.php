<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use ProduitType as GlobalProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminProductController extends AbstractController
{
    #[Route('/admin', name: 'admin_products_list')]
    public function index(ProduitRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); // accès admin
        return $this->render('admin_product/index.html.twig', [
            'produits' => $repo->findAll(),
        ]);
    }

    #[Route('/admin/ajouter', name: 'admin_product_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $produit = new Produit();
        $form = $this->createForm(GlobalProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taillesText = $form->get('taille')->getData();
            $taillesArray = array_map('trim', explode(',', $taillesText));
            $produit->setTaille($taillesArray);

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move($this->getParameter('kernel.project_dir').'/public/images/produits', $newFilename);
                $produit->setImage($newFilename);
            }



            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès.');
            return $this->redirectToRoute('admin_products_list');
        }

        return $this->render('admin_product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/modifier/{id}', name: 'admin_product_edit')]
    public function edit(Produit $produit, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(GlobalProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Produit modifié avec succès.');
            return $this->redirectToRoute('admin_products_list');
        }

        return $this->render('admin_product/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/supprimer/{id}', name: 'admin_product_delete', methods:['POST'])]
    public function delete(Produit $produit, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($produit);
        $em->flush();

        $this->addFlash('success', 'Produit supprimé.');
        return $this->redirectToRoute('admin_products_list');
    }
}
