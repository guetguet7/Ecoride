<?php

namespace App\Controller;

use App\Entity\UserProfile;
use App\Form\ProfileType;
use App\Repository\AvisRepository;
use App\Repository\RidesRepository;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function show(EntityManagerInterface $em, AvisRepository $avisRepository, RidesRepository $ridesRepository): Response
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $profileCreated = false;
        $profile = $user->getProfile() ?? new UserProfile();
        if (!$user->getProfile()) {
            $profile->setUser($user);
            $user->setProfile($profile);
            $em->persist($profile);
            $profileCreated = true;
        }

        if ($profileCreated) {
            $em->flush();
        }

        $receivedReviews = $avisRepository->findBy(
            ['driver' => $user, 'statut' => 'published'],
            ['createdAt' => 'DESC']
        );

        $activeRides = $ridesRepository->createQueryBuilder('r')
            ->andWhere('r.user = :user')
            ->andWhere('r.status IN (:statuses)')
            ->setParameter('user', $user)
            ->setParameter('statuses', ['active', 'in_progress'])
            ->orderBy('r.dateHeureDepart', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'receivedReviews' => $receivedReviews,
            'activeRides' => $activeRides,
        ]);
    }

    #[Route('/profile/history', name: 'profile_history', methods: ['GET'])]
    public function history(
        RidesRepository $ridesRepository,
        ParticipationRepository $participationRepository
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $ridesAsDriver = $ridesRepository->findBy(
            ['user' => $user, 'status' => 'finished'],
            ['dateHeureArrivee' => 'DESC']
        );

        $ridesAsPassenger = $participationRepository->createQueryBuilder('p')
            ->join('p.ride', 'r')
            ->andWhere('p.user = :user')
            ->andWhere('r.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'finished')
            ->orderBy('r.dateHeureArrivee', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('profile/history.html.twig', [
            'ridesAsDriver' => $ridesAsDriver,
            'ridesAsPassenger' => $ridesAsPassenger,
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $profileCreated = false;
        $profile = $user->getProfile() ?? new UserProfile();
        if (!$user->getProfile()) {
            $profile->setUser($user);
            $user->setProfile($profile);
            $em->persist($profile);
            $profileCreated = true;
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedPhoto = $form->get('photoFile')->getData();
            if ($uploadedPhoto) {
                // Gestion de l'upload photo de profil
                $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/photos';
                if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                    throw new \RuntimeException(sprintf('Le répertoire "%s" est inaccessible.', $uploadDir));
                }

                $extension = $uploadedPhoto->guessExtension() ?: 'bin';
                $newFilename = uniqid('profile_', true) . '.' . $extension;

                // Supprime l'ancienne photo si présente
                $oldPhoto = $profile->getPhoto();
                if ($oldPhoto) {
                    $oldPath = $uploadDir . '/' . $oldPhoto;
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                try {
                    $uploadedPhoto->move($uploadDir, $newFilename);
                    $profile->setPhoto($newFilename);
                } catch (FileException) {
                    $this->addFlash('error', 'Impossible de téléverser la photo de profil.');

                    return $this->render('profile/edit.html.twig', [
                        'user' => $user,
                        'form' => $form->createView(),
                    ]);
                }
            }

            $user->setUpdateAt(new \DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('app_profile');
        }

        if ($profileCreated) {
            $em->flush();
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
