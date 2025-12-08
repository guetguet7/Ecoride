<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class VoitureController extends AbstractController
{
    #[Route('/profile/voitures', name: 'voiture_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        /** @var User $user */

        if ($request->isMethod('POST')) {
            $data = $request->request->all('voiture');
            $voiture = new Voiture();
            $voiture->setUser($user);
            $voiture->setModele($data['modele'] ?? '');
            $voiture->setImmatriculation($data['immatriculation'] ?? '');
            $isElectric = !empty($data['energie_electrique']);
            $voiture->setEnergie($isElectric ? 'Electrique' : 'Non électrique');
            $voiture->setCouleur($data['couleur'] ?? '');
            $voiture->setDatePremiereImmatriculation($data['datePremiereImmatriculation'] ?? '');

            $em->persist($voiture);
            $em->flush();

            $this->addFlash('success', 'Voiture ajoutée.');
            return $this->redirectToRoute('voiture_index');
        }

        return $this->render('voiture/index.html.twig', [
            'voitures' => $user->getVoitures(),
        ]);
    }

    #[Route('/profile/voitures/{id}/delete', name: 'voiture_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }
        /** @var User $user */

        $voiture = $em->getRepository(Voiture::class)->find($id);
        if (!$voiture || $voiture->getUser() !== $user) {
            $this->addFlash('error', 'Voiture introuvable ou non autorisée.');
            return $this->redirectToRoute('voiture_index');
        }

        if ($this->isCsrfTokenValid('delete_voiture_' . $voiture->getId(), $request->request->get('_token'))) {
            $em->remove($voiture);
            $em->flush();
            $this->addFlash('success', 'Voiture supprimée.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('voiture_index');
    }
}
