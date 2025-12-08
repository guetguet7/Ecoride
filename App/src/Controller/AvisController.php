<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Rides;
use App\Entity\User;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/avis')]
final class AvisController extends AbstractController
{
    #[Route('/ride/{id}/nouveau', name: 'ride_review_new', methods: ['GET', 'POST'])]
    public function new(
        Rides $ride,
        Request $request,
        EntityManagerInterface $em,
        ParticipationRepository $participationRepository,
        AvisRepository $avisRepository
    ): Response {
        /** @var User|null $user */
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $driver = $ride->getUser();
        if (!$driver) {
            $this->addFlash('error', 'Impossible de déterminer le conducteur pour ce trajet.');
            return $this->redirectToRoute('app_covoiturage');
        }

        if ($driver === $user) {
            $this->addFlash('error', 'Vous ne pouvez pas laisser d\'avis sur votre propre trajet.');
            return $this->redirectToRoute('ride_show', ['id' => $ride->getId()]);
        }

        $participation = $participationRepository->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->andWhere('p.ride = :ride')
            ->andWhere('p.status IN (:statuses)')
            ->setParameter('user', $user)
            ->setParameter('ride', $ride)
            ->setParameter('statuses', ['confirmed', 'completed'])
            ->getQuery()
            ->getOneOrNullResult();

        if (!$participation) {
            $this->addFlash('error', 'Vous devez avoir participé à ce trajet pour laisser un avis.');
            return $this->redirectToRoute('ride_show', ['id' => $ride->getId()]);
        }

        $existingReview = $avisRepository->findOneBy(['author' => $user, 'ride' => $ride]);
        if ($existingReview) {
            $this->addFlash('info', 'Vous avez déjà laissé un avis pour ce trajet.');
            return $this->redirectToRoute('ride_show', ['id' => $ride->getId(), '_fragment' => 'ride-reviews']);
        }

        $review = new Avis();
        $form = $this->createForm(AvisType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validation: l'auteur est le passager, le driver est le propriétaire du trajet, avis en attente
            $review->setAuthor($user);
            $review->setDriver($driver);
            $review->setRide($ride);
            $review->setStatut('pending');

            $em->persist($review);
            $em->flush();

            $this->addFlash('success', 'Avis enregistré et en attente de validation par un employé.');

            return $this->redirectToRoute('profile_history');
        }

        return $this->render('avis/new.html.twig', [
            'ride' => $ride,
            'driver' => $driver,
            'form' => $form->createView(),
        ]);
    }
}
