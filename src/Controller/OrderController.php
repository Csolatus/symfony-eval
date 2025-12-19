<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class OrderController extends AbstractController
{
    #[Route('/order/{reference}', name: 'app_order_show')]
    public function show(
        #[MapEntity(mapping: ['reference' => 'reference'])]
        Order $order
    ): Response {
        // Ensure the logged-in user owns the order
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
}
