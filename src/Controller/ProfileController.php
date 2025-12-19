<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        // Création du formulaire lié à l'utilisateur connecté
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide, on sauvegarde les changements
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // Doctrine détecte automatiquement les modifications sur l'objet $user

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/profile/delete', name: 'app_profile_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Cette action est sensible, on vérifie manuellement le token CSRF pour éviter les suppressions accidentelles/malveillantes
        if ($this->isCsrfTokenValid('delete_account', $request->request->get('_token'))) {
            // Il faut "détacher" l'utilisateur de la sécurité avant de le supprimer, sinon la session devient invalide
            $this->container->get('security.token_storage')->setToken(null);

            $em->remove($user);
            $em->flush(); // Suppression effective en base de données

            // On invalide la session pour déconnecter proprement l'utilisateur
            $request->getSession()->invalidate();
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide pour la suppression du compte.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->redirectToRoute('app_home');
    }
}
