<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\User;
use App\Entity\Produit;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @Route("/commande")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/index", name="commande_index", methods={"GET"})
     */
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/", name="commande")
     */
    public function afficher(sessioninterface $session, CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findBy([
            'user' => $session->get('user'),
        ]);

        // dd($commandes);
        return $this->render('commande/userCommande.html.twig', [
            'commandes' => $commandes
        ]);
    }

    /**
     * @Route("/annuler", name="commande_annuler")
     */
    public function annuler(sessioninterface $session, CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/userCommande.html.twig', [
            'commandes' => $commandeRepository->findOneBy([
                'id' => $session->get('user')->getId(),
            ]),
        ]);
    }

    /**
     * @Route("/new", name="commande_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $commande->setCreatedAt(new \DateTime());
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ajouter/", name="commande_ajouter")
     */
    public function ajouter(sessioninterface $session, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): Response
    {

        $user_id = $session->get('user')->getId(); // id du user connecté
        $items = $session->get('infoPanier'); // panier with data
        $total = $session->get('total'); //somme totale du panier

        //dump($infoPanier);
        //dd($total);

        $commande = new Commande();
        $user = $entityManager->getRepository(User::class)->find($user_id);
        $commande->setUser($user);
        $commande->setItems($items);

        //pour mettre a jour les QTE dans la base de donnée.
        foreach ($items as $item) {
            $qteBDD = $item['produit']->getQte();
            $qte = $item['qte'];
            //appeler la base de donnée
            $produit = $entityManager->getRepository(Produit::class)->find($item['produit']->getId());
            if ($qteBDD >= $qte) {
                $produit->setQte($qteBDD - $qte);
            } else {
                $produit->setQte(0);
                $item['qte'] = $qte - $qteBDD;
            }
        }


        $commande->setPrixTotal($total);
        $commande->setEtat(1);
        $commande->setCreatedAt(new \DateTime());


        $entityManager->persist($commande);
        $entityManager->flush();

        // Effacer le panier

        $session->set('panier', []);
        $session->set('infopanier', []);
        $session->set('total', 0);

        return $this->redirectToRoute('commande', [],);
    }

    /**
     * @Route("/{id}", name="commande_show", methods={"GET"})
     */
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="commande_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="commande_delete", methods={"POST"})
     */
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
    }
}