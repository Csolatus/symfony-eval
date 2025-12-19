<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            // Sécurité : Vérification CSRF manuelle car ce n'est pas un formulaire Symfony standard
            if (!$this->isCsrfTokenValid('contact', $request->request->get('_token'))) {
                $this->addFlash('error', 'Jeton CSRF invalide.');
                return $this->redirectToRoute('app_contact');
            }

            $name = $request->request->get('name');
            $email = $request->request->get('email');
            $message = $request->request->get('message');

            // Validation manuelle simple : on refuse si un champ est vide
            if (empty($name) || empty($email) || empty($message)) {
                $this->addFlash('error', 'Veuillez remplir tous les champs.');
                return $this->render('contact/index.html.twig'); // Réaffiche le formulaire avec l'erreur
            }

            // Ici, on pourrait envoyer un vrai email via MailerItnerface
            // Pour l'instant, on simule l'envoi avec un message flash
            $this->addFlash('success', 'Votre message a été envoyé avec succès !');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig');
    }
}
