<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Entity\Rides;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ParticipationController extends AbstractController
{
    #[Route('/rides/{id}/participate', name: 'ride_participate', methods: ['POST'])]
    public function participate(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_register');
        }

        $ride = $em->getRepository(Rides::class)->find($id);
        if (!$ride) {
            $this->addFlash('error', 'Trajet introuvable.');
            return $this->redirectToRoute('app_covoiturage');
        }

        if (!in_array($ride->getStatus(), ['active'], true)) {
            $this->addFlash('error', 'Ce trajet n\'est plus ouvert aux réservations.');
            return $this->redirectToRoute('ride_show', ['id' => $id]);
        }

        if ($ride->getUser() === $user) {
            // Interdit au conducteur de réserver son propre trajet
            $this->addFlash('error', 'Vous ne pouvez pas participer à votre propre trajet.');
            return $this->redirectToRoute('ride_show', ['id' => $id]);
        }

        if (!$this->isCsrfTokenValid('participate_ride_' . $ride->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('ride_show', ['id' => $id]);
        }

        $existing = $em->getRepository(Participation::class)->findOneBy(['user' => $user, 'ride' => $ride]);
        if ($existing) {
            $this->addFlash('info', 'Vous participez déjà à ce trajet.');
            return $this->redirectToRoute('ride_show', ['id' => $id]);
        }

        $participation = new Participation();
        $participation->setUser($user);
        $participation->setRide($ride);
        $participation->setAmount($ride->getPrix());
        $participation->setStatus('confirmed');

        // Vérifie les places restantes
        $placesRestantes = (int) $ride->getNbplace();
        if ($placesRestantes <= 0) {
            $this->addFlash('error', 'Plus de places disponibles pour ce trajet.');
            return $this->redirectToRoute('ride_show', ['id' => $id]);
        }

        // Vérifie le crédit disponible
        $amount = $ride->getPrix();
        if ($user->getCredits() < $amount) {
            $this->addFlash('error', 'Crédits insuffisants pour ce trajet.');
            return $this->redirectToRoute('ride_show', ['id' => $id]);
        }

        // Débite le compte dès la confirmation
        $user->setCredits($user->getCredits() - $amount);
        // Décrémente les places disponibles
        $ride->setNbplace((string) max(0, $placesRestantes - 1));

        $em->persist($participation);
        $em->persist($user);
        $em->persist($ride);
        $em->flush();

        $this->addFlash('success', 'Participation confirmée.');

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/participations/{id}/cancel', name: 'participation_cancel', methods: ['POST'])]
    public function cancel(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $participation = $em->getRepository(Participation::class)->find($id);
        if (!$participation || $participation->getUser() !== $user) {
            $this->addFlash('error', 'Participation introuvable ou non autorisée.');
            return $this->redirectToRoute('app_profile');
        }

        if (in_array($participation->getStatus(), ['completed'], true) || in_array($participation->getRide()?->getStatus(), ['in_progress', 'finished'], true)) {
            $this->addFlash('error', 'Ce trajet est déjà terminé, annulation impossible.');
            return $this->redirectToRoute('app_profile');
        }

        if ($this->isCsrfTokenValid('cancel_participation_' . $participation->getId(), $request->request->get('_token'))) {
            // Remboursement des crédits dépensés
            $amount = $participation->getAmount();
            $user->setCredits($user->getCredits() + $amount);
            // Réouvre une place si le trajet est toujours actif
            $ride = $participation->getRide();
            if ($ride && $ride->getStatus() === 'active') {
                $ride->setNbplace((string) ((int) $ride->getNbplace() + 1));
                $em->persist($ride);
            }

            $em->remove($participation);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Participation annulée et crédits remboursés.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_profile');
    }
}
