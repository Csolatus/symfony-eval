<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    #[Route('/checkout', name: 'app_checkout')]
    public function index(\Symfony\Component\HttpFoundation\Session\SessionInterface $session, \App\Repository\ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $cart = $session->get('cart', []);
        $cartData = [];
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
                $total += $product->getPrice() * $quantity;
            }
        }

        if (empty($cartData)) {
            return $this->redirectToRoute('app_cart');
        }

        return $this->render('checkout/index.html.twig', [
            'items' => $cartData,
            'total' => $total,
        ]);
    }

    #[Route('/checkout/process', name: 'app_checkout_process', methods: ['POST'])]
    public function process(
        \Symfony\Component\HttpFoundation\Request $request,
        \Symfony\Component\HttpFoundation\Session\SessionInterface $session,
        \Doctrine\ORM\EntityManagerInterface $em,
        \App\Repository\ProductRepository $productRepository
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User) {
            return $this->redirectToRoute('app_login');
        }

        // 1. Sécurité : Vérification du jeton CSRF pour empêcher les soumissions frauduleuses
        if (!$this->isCsrfTokenValid('checkout', $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton de sécurité invalide. Veuillez réessayer.');
            return $this->redirectToRoute('app_checkout');
        }

        // 2. Mise à jour de l'adresse : On enregistre les dernières infos de livraison saisies par l'utilisateur
        $user->setFullName($request->request->get('fullName'));
        $user->setAddress($request->request->get('address'));
        $user->setCity($request->request->get('city'));
        $user->setPostalCode($request->request->get('postalCode'));

        // 3. Création de la commande : Initialisation de l'entité Order
        $order = new \App\Entity\Order();
        $order->setUser($user);
        $order->setReference(uniqid()); // Génère une référence unique pour le suivi
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setStatus('PENDING'); // Statut initial

        $em->persist($order);

        // 4. Ajout des produits : On transforme le contenu du panier (session) en lignes de commande (base de données)
        $cart = $session->get('cart', []);
        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $orderItem = new \App\Entity\OrderItem();
                $orderItem->setOrderPurchase($order);
                $orderItem->setProduct($product);
                $orderItem->setQuantity($quantity);
                $orderItem->setPrice($product->getPrice()); // Important : On fige le prix au moment de l'achat

                $em->persist($orderItem);
            }
        }

        $em->flush(); // Exécute toutes les requêtes SQL (création commande + lignes)

        // 5. Nettoyage : Une fois la commande passée, on vide le panier
        $session->remove('cart');

        $this->addFlash('success', 'Commande passée avec succès !');

        return $this->redirectToRoute('app_order_show', ['reference' => $order->getReference()]);
    }
}
