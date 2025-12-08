<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard', methods: ['GET'])]
    public function index(
        Request $request,
        UserRepository $userRepository,
        Connection $conn
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Stats trajets par jour
        $ridesPerDay = $conn->fetchAllAssociative('SELECT DATE(date_heure_depart) AS day, COUNT(id) AS rideCount FROM rides GROUP BY day ORDER BY day');

        // Stats crédits gagnés par jour
        $earningsPerDay = $conn->fetchAllAssociative('SELECT DATE(created_at) AS day, SUM(amount) AS totalCredits FROM participation GROUP BY day ORDER BY day');

        $totalCredits = (int) $conn->fetchOne('SELECT COALESCE(SUM(amount), 0) FROM participation');

        $ridesLabels = array_map(static fn(array $r) => $r['day'], $ridesPerDay);
        $ridesValues = array_map(static fn(array $r) => (int) $r['rideCount'], $ridesPerDay);
        $earningsLabels = array_map(static fn(array $e) => $e['day'], $earningsPerDay);
        $earningsValues = array_map(static fn(array $e) => (int) $e['totalCredits'], $earningsPerDay);

        $users = $userRepository->findAll();
        $employees = array_filter($users, fn (User $u) => in_array('ROLE_EMPLOYEE', $u->getRoles(), true));

        $perPage = 3;
        $empPage = max(1, (int) $request->query->get('empPage', 1));
        $userPage = max(1, (int) $request->query->get('userPage', 1));

        $empTotal = count($employees);
        $empPages = max(1, (int) ceil($empTotal / $perPage));
        $empPage = min($empPage, $empPages);
        $employeesPage = array_slice(array_values($employees), ($empPage - 1) * $perPage, $perPage);

        $userTotal = count($users);
        $userPages = max(1, (int) ceil($userTotal / $perPage));
        $userPage = min($userPage, $userPages);
        $usersPage = array_slice($users, ($userPage - 1) * $perPage, $perPage);

        return $this->render('admin/index.html.twig', [
            'ridesPerDay' => $ridesPerDay,
            'earningsPerDay' => $earningsPerDay,
            'totalCredits' => $totalCredits,
            'employees' => $employeesPage,
            'users' => $usersPage,
            'ridesLabels' => $ridesLabels,
            'ridesValues' => $ridesValues,
            'earningsLabels' => $earningsLabels,
            'earningsValues' => $earningsValues,
            'empPage' => $empPage,
            'empPages' => $empPages,
            'userPage' => $userPage,
            'userPages' => $userPages,
        ]);
    }

    #[Route('/employee/create', name: 'admin_employee_create', methods: ['POST'])]
    public function createEmployee(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = $request->request->all('employee');
        $pseudo = trim((string) ($data['pseudo'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        if ($pseudo === '' || $email === '' || $password === '') {
            $this->addFlash('error', 'Pseudo, email et mot de passe sont requis.');
            return $this->redirectToRoute('admin_dashboard');
        }

        $user = new User();
        $user->setPseudo($pseudo);
        $user->setEmail($email);
        $user->setPassword($passwordHasher->hashPassword($user, $password));
        $user->setRoles(['ROLE_EMPLOYEE']);
        $user->setCreateAt(new DateTimeImmutable());
        $user->setIsVerified(true);

        $userRepository->save($user, true);

        $this->addFlash('success', 'Compte employé créé.');

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/user/{id}/toggle-suspend', name: 'admin_user_toggle_suspend', methods: ['POST'])]
    public function toggleSuspend(int $id, Request $request, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $userRepository->find($id);
        if (!$user) {
            $this->addFlash('error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('admin_dashboard');
        }

        if (!$this->isCsrfTokenValid('toggle_suspend_' . $user->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('admin_dashboard');
        }

        $user->setSuspended(!$user->isSuspended());
        $userRepository->save($user, true);

        $this->addFlash('success', $user->isSuspended() ? 'Compte suspendu.' : 'Compte réactivé.');

        return $this->redirectToRoute('admin_dashboard');
    }
}
