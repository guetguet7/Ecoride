<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Repository\RidesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CovoiturageController extends AbstractController
{
    #[Route('/covoiturage', name: 'app_covoiturage')]
    public function index(Request $request, RidesRepository $ridesRepository): Response
    {
        $depart = trim((string) $request->query->get('depart', ''));
        $arrivee = trim((string) $request->query->get('arrivee', ''));
        $dateStr = $request->query->get('date');

        $qb = $ridesRepository->createQueryBuilder('r');
        $qb->andWhere('r.status IN (:openStatuses)')
           ->setParameter('openStatuses', ['active', 'in_progress']);

        if ($depart !== '') {
            $qb->andWhere('LOWER(r.lieuDepart) LIKE :depart')
               ->setParameter('depart', '%' . strtolower($depart) . '%');
        }

        if ($arrivee !== '') {
            $qb->andWhere('LOWER(r.lieuArrivee) LIKE :arrivee')
               ->setParameter('arrivee', '%' . strtolower($arrivee) . '%');
        }

        if (!empty($dateStr)) {
            try {
                $date = new DateTime($dateStr);
                $start = (clone $date)->setTime(0, 0, 0);
                $end   = (clone $date)->setTime(23, 59, 59);
                $qb->andWhere('r.dateHeureDepart BETWEEN :start AND :end')
                   ->setParameter('start', $start)
                   ->setParameter('end', $end);
            } catch (Exception) {
                // ignore invalid date
            }
        }

        $rides = $qb->orderBy('r.dateHeureDepart', 'ASC')->getQuery()->getResult();

        return $this->render('covoiturage/index.html.twig', [
            'rides' => $rides,
            'filters' => [
                'depart' => $depart,
                'arrivee' => $arrivee,
                'date' => $dateStr,
            ],
        ]);
    }
}
