<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function index(ProduitRepository $repo): Response
    {
        $produits = $repo->findAll();

        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
            'produits' => $produits,
        ]);
    }
}