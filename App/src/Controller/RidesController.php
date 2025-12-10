<?php

namespace App\Controller;

use App\Entity\Rides;
use App\Entity\User;
use App\Entity\Voiture;
use App\Repository\RidesRepository;
use App\Repository\AvisRepository;
use App\Repository\ParticipationRepository;
use DateTime;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RidesController extends AbstractController
{
    #[Route('/rides/edit', name: 'rides_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if (!in_array($user->getRoleType(), ['driver', 'both'], true)) {
            // Empêche la création de trajet si le rôle n'est pas chauffeur
            $this->addFlash('error', 'Vous devez choisir d\'être chauffeur dans votre profil pour proposer un trajet.');
            return $this->redirectToRoute('app_profile_edit');
        }

        $profile = $user->getProfile();
        $missingProfileFields = [];
        if (!$profile || !$profile->getNom()) {
            $missingProfileFields[] = 'nom';
        }
        if (!$profile || !$profile->getPrenom()) {
            $missingProfileFields[] = 'prénom';
        }
        if (!$profile || !$profile->getTelephone()) {
            $missingProfileFields[] = 'téléphone';
        }
        if (!$profile || !$profile->getAdresse()) {
            $missingProfileFields[] = 'adresse';
        }

        if (!empty($missingProfileFields)) {
            // Forcer la complétion du profil avant de proposer un trajet
            $this->addFlash('error', 'Veuillez compléter votre profil (' . implode(', ', $missingProfileFields) . ') avant de proposer un trajet.');
            return $this->redirectToRoute('app_profile_edit');
        }

        if ($user->getVoitures()->count() === 0) {
            // Un chauffeur doit avoir au moins une voiture
            $this->addFlash('error', 'Veuillez ajouter une voiture avant de proposer un trajet.');
            return $this->redirectToRoute('voiture_index');
        }

        $profilePhoto = $user?->getProfile()?->getPhoto();
        if (!$profilePhoto) {
            // Photo obligatoire pour le conducteur
            $this->addFlash('error', 'Veuillez ajouter une photo à votre profil avant de proposer un trajet.');
            return $this->redirectToRoute('app_profile_edit');
        }

        $ride = new Rides();

        if ($request->isMethod('POST')) {
            $data = $request->request->all('ride');

            $ride->setPseudo($data['pseudo'] ?? $user->getPseudo() ?? '');
            $ride->setUser($user);
            $ride->setNbplace((int) ($data['nbplace'] ?? 0));
            $ride->setPrix((int) ($data['prix'] ?? 0));
            $ride->setLieuDepart($data['lieuDepart'] ?? '');
            $ride->setLieuArrivee($data['lieuArrivee'] ?? '');

            $dateDepart = !empty($data['dateHeureDepart']) ? new DateTime($data['dateHeureDepart']) : null;
            $dateArrivee = !empty($data['dateHeureArrivee']) ? new DateTime($data['dateHeureArrivee']) : null;

            if ($dateDepart !== null) {
                $ride->setDateHeureDepart($dateDepart);
            }
            if ($dateArrivee !== null) {
                $ride->setDateHeureArrivee($dateArrivee);
            }

            // Photo récupérée automatiquement depuis le profil utilisateur si dispo
            $ride->setPhoto($profilePhoto);

            $em->persist($ride);
            $em->flush();

            $this->addFlash('success', 'Trajet enregistré.');

            return $this->redirectToRoute('app_covoiturage');
        }

        return $this->render('rides/edit.html.twig', [
            'ride' => $ride,
        ]);
    }

    #[Route('/rides/{id}', name: 'ride_show', methods: ['GET'])]
    public function show(
        int $id,
        RidesRepository $ridesRepository,
        AvisRepository $avisRepository,
        ParticipationRepository $participationRepository
    ): Response
    {
        $ride = $ridesRepository->find($id);
        if (!$ride) {
            throw $this->createNotFoundException('Trajet introuvable.');
        }

        /** @var User|null $driver */
        $driver = $ride->getUser();
        $firstCar = $driver?->getVoitures()->first();
        $voiture = $firstCar instanceof Voiture ? $firstCar : null;
        $driverNote = $driver?->getRating() ?? 0;
        $reviews = $driver ? $avisRepository->findBy(['driver' => $driver, 'statut' => 'published'], ['createdAt' => 'DESC']) : [];

        $currentUser = $this->getUser();
        $canReview = false;
        $userReview = null;
        $hasParticipation = false;
        $isOwner = $currentUser && $driver && $currentUser === $driver;
        $isFinished = $ride->getStatus() === 'finished';

        if ($currentUser instanceof User && $driver && $currentUser !== $driver) {
            $participation = $participationRepository->findOneBy([
                'user' => $currentUser,
                'ride' => $ride,
                'status' => 'confirmed',
            ]);

            $hasParticipation = (bool) $participation;

            if ($participation) {
                $userReview = $avisRepository->findOneBy([
                    'author' => $currentUser,
                    'ride' => $ride,
                ]);

                $rideEnd = $ride->getDateHeureArrivee();
                $rideFinished = !$rideEnd instanceof \DateTimeInterface || $rideEnd <= new \DateTime();
                // Laisser un avis seulement si la participation existe et que le trajet est fini
                $canReview = !$userReview && $rideFinished;
            }
        }
        
        return $this->render('rides/show.html.twig', [
            'ride' => $ride,
            'driver' => $driver,
            'voiture' => $voiture,
            'driverNote' => $driverNote,
            'reviews' => $reviews,
            'canReview' => $canReview,
            'userReview' => $userReview,
            'hasParticipation' => $hasParticipation,
            'isOwner' => $isOwner,
            'isFinished' => $isFinished,
        ]);
    }

    
    #[Route('/rides/{id}/delete', name: 'ride_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $ride = $em->getRepository(Rides::class)->find($id);
        if (!$ride || $ride->getUser() !== $user) {
            $this->addFlash('error', 'Trajet introuvable ou non autorisé.');
            return $this->redirectToRoute('app_profile');
        }

        if ($this->isCsrfTokenValid('delete_ride_' . $ride->getId(), $request->request->get('_token'))) {
            $em->remove($ride);
            $em->flush();
            $this->addFlash('success', 'Trajet supprimé.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_profile');
    }

    #[Route('/rides/{id}/start', name: 'ride_start', methods: ['POST'])]
    public function start(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $ride = $em->getRepository(Rides::class)->find($id);
        if (!$user || !$ride || $ride->getUser() !== $user) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('ride_show', ['id' => $id]);
        }

        if (!$this->isCsrfTokenValid('start_ride_' . $ride->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('ride_show', ['id' => $id]);
        }

        $ride->setStatus('in_progress');
        $em->flush();

        $this->addFlash('success', 'Trajet démarré.');

        return $this->redirectToRoute('ride_show', ['id' => $id]);
    }

    #[Route('/rides/{id}/finish', name: 'ride_finish', methods: ['POST'])]
    public function finish(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $user = $this->getUser();
        $ride = $em->getRepository(Rides::class)->find($id);
        if (!$user || !$ride || $ride->getUser() !== $user) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('ride_show', ['id' => $id]);
        }

        if (!$this->isCsrfTokenValid('finish_ride_' . $ride->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('ride_show', ['id' => $id]);
        }

        $ride->setStatus('finished');

        $driver = $ride->getUser();
        $driverCredits = $driver?->getCredits() ?? 0;

        foreach ($ride->getParticipations() as $participation) {
            if ($driver && $participation->getStatus() === 'confirmed') {
                $driverCredits += $participation->getAmount();
                $participation->setStatus('completed');
                $em->persist($participation);
            }

            $passenger = $participation->getUser();
            if (!$passenger || !$passenger->getEmail()) {
                continue;
            }

            $reviewUrl = $this->generateUrl('ride_review_new', ['id' => $ride->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            $email = (new TemplatedEmail())
                ->from(new Address('contact@ecoride.test', 'EcoRide'))
                ->to(new Address($passenger->getEmail(), $passenger->getPseudo()))
                ->subject('Votre trajet est terminé : laissez un avis')
                ->htmlTemplate('emails/review_invite.html.twig')
                ->context([
                    'driver' => $user,
                    'ride' => $ride,
                    'reviewUrl' => $reviewUrl,
                ]);

            $mailer->send($email);
        }

        if ($driver) {
            $driver->setCredits($driverCredits);
            $em->persist($driver);
        }

        $em->flush();

        $this->addFlash('success', 'Trajet terminé. Les passagers ont été invités à laisser un avis.');

        return $this->redirectToRoute('ride_show', ['id' => $id]);
    }
}
