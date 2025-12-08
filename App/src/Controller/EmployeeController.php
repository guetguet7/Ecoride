<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employee')]
final class EmployeeController extends AbstractController
{
    #[Route('', name: 'employee_dashboard', methods: ['GET'])]
    public function dashboard(Request $request, AvisRepository $avisRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        $pendingReviews = $avisRepository->createQueryBuilder('a')
            ->leftJoin('a.author', 'author')->addSelect('author')
            ->leftJoin('a.driver', 'driver')->addSelect('driver')
            ->leftJoin('a.ride', 'ride')->addSelect('ride')
            ->andWhere('a.statut = :pending')
            ->setParameter('pending', 'pending')
            ->orderBy('a.createdAt', 'ASC')
            ->getQuery()
            ->getResult();

        $flaggedRides = $avisRepository->createQueryBuilder('a')
            ->leftJoin('a.author', 'fauthor')->addSelect('fauthor')
            ->leftJoin('a.driver', 'fdriver')->addSelect('fdriver')
            ->leftJoin('a.ride', 'fride')->addSelect('fride')
            ->andWhere('a.note <= :lowNote')
            ->andWhere('a.statut IN (:validStatuses)')
            ->setParameter('lowNote', 2)
            ->setParameter('validStatuses', ['pending', 'published'])
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $perPage = 3;
        $pendingPage = max(1, (int) $request->query->get('pendingPage', 1));
        $flaggedPage = max(1, (int) $request->query->get('flaggedPage', 1));

        $pendingTotal = count($pendingReviews);
        $pendingPages = max(1, (int) ceil($pendingTotal / $perPage));
        $pendingPage = min($pendingPage, $pendingPages);
        $pendingReviewsPage = array_slice($pendingReviews, ($pendingPage - 1) * $perPage, $perPage);

        $flaggedTotal = count($flaggedRides);
        $flaggedPages = max(1, (int) ceil($flaggedTotal / $perPage));
        $flaggedPage = min($flaggedPage, $flaggedPages);
        $flaggedRidesPage = array_slice($flaggedRides, ($flaggedPage - 1) * $perPage, $perPage);

        return $this->render('employee/index.html.twig', [
            'pendingReviews' => $pendingReviewsPage,
            'flaggedRides' => $flaggedRidesPage,
            'pendingPage' => $pendingPage,
            'pendingPages' => $pendingPages,
            'flaggedPage' => $flaggedPage,
            'flaggedPages' => $flaggedPages,
        ]);
    }

    #[Route('/reviews/{id}/approve', name: 'employee_review_approve', methods: ['POST'])]
    public function approve(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        $review = $em->getRepository(Avis::class)->find($id);
        if (!$review) {
            $this->addFlash('error', 'Avis introuvable.');
            return $this->redirectToRoute('employee_dashboard');
        }

        if (!$this->isCsrfTokenValid('approve_review_' . $review->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('employee_dashboard');
        }

        // Validation manuelle d'un avis en attente
        $review->setStatut('published');
        $em->persist($review);
        $em->flush();

        $this->addFlash('success', 'Avis validé et publié.');

        return $this->redirectToRoute('employee_dashboard');
    }

    #[Route('/reviews/{id}/reject', name: 'employee_review_reject', methods: ['POST'])]
    public function reject(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        $review = $em->getRepository(Avis::class)->find($id);
        if (!$review) {
            $this->addFlash('error', 'Avis introuvable.');
            return $this->redirectToRoute('employee_dashboard');
        }

        if (!$this->isCsrfTokenValid('reject_review_' . $review->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('employee_dashboard');
        }

        $review->setStatut('rejected');
        $em->persist($review);
        $em->flush();

        $this->addFlash('success', 'Avis refusé.');

        return $this->redirectToRoute('employee_dashboard');
    }
}
