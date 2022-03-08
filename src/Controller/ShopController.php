<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function index(Request $request, ProduitRepository $repo, PaginatorInterface $paginator): Response
    {
        $data = $repo->findAll();

        $produits = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            4
        );
        //dd($session->get('user')); pour tester si la variable session_user est bien definie

        return $this->render('shop/index.html.twig', [
            'produits' => $produits,
        ]);
    }
}