<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(sessioninterface $session, ProduitRepository $produitRepository): Response
    {
        $panier = $session->get('panier', []);
        if (!empty($panier)) {
            $panier = array_filter($panier); //supprimer les cases vides si il  en a par erreur
            unset($panier[""]);
        }
        $infoPanier = [];
        foreach ($panier as $id => $qte) {
            $infoPanier[] = [
                'produit' => $produitRepository->find($id),
                'qte' => $qte,
            ];
        }
        $total = 0;

        foreach ($infoPanier as $item) {
            $total += $item['produit']->getPrix() * $item['qte'];
        }

        $session->set('infoPanier', $infoPanier); //passer le panier with data en globale
        $session->set('total', $total); //passer le total du panier au globale 

        return $this->render('panier/index.html.twig', [
            'items' => $infoPanier,
            'total' => $total
        ]);
    }

    /**
     * @Route("/panier/ajouter/{id}", name="panier_ajouter")
     */
    //ici session interface nous renvoi directement la session sans passer par la request
    public function ajouter(Produit $produit, SessionInterface $session): Response
    {
        // //initialiser la session
        // $session = $request->getSession();
        // ====================================
        //prendre le panier,le mettre comme tableau vide si il exite pas.
        //LE PANIER EST REPRESENTER PAR UN TABLEAU ASSOCIATIF COMME SUIT
        // $panier[IDproduit] => [qte]
        $panier = $session->get('panier', []);
        $id = $produit->getId();
        //on verifie si le produit existe, pour savoir l'ajouter ou l'incrementer
        if (empty($panier[$id]) && ($produit->getQte() > 0))
            $panier[$id] = 1;
        else if (!empty($panier[$id]) && ($produit->getQte() > $panier[$id]))
            $panier[$id] += 1;
        else {
            return $this->redirectToRoute('shop');
        }

        //on envoi vers la session
        $session->set('panier', $panier);
        //dd($session->get('panier'));
        return $this->redirectToRoute('panier');
    }

    /**
     *@Route("/panier/modifierQte",name="panier_modifier_qte") 
     **/
    public function modifierQte(Request $request, SessionInterface $session, entityManagerInterface $entityManager)
    {
        $id = $request->get('id');
        $qte = $request->get('qte');
        $qteBDD = $entityManager->getRepository(Produit::class)->find($id)->getQte();
        $panier = $session->get('panier', []);

        if ($qteBDD >= $qte) {
            $panier[$id] = $qte;
        } else {
            $qte = $qteBDD;
            $panier[$id] = $qte;
        }

        // dd($panier);

        $session->set('panier', $panier);

        return $this->redirectToRoute('panier');
    }


    /**
     *@Route("/panier/supprimer/{id}",name="panier_supprimer") 
     **/
    public function supprimer(Produit $produit, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$produit->getId()]))
            unset($panier[$produit->getId()]);

        $session->set('panier', $panier);

        return $this->redirectToRoute('panier');
    }
}