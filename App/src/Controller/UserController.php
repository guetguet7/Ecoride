<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class UserController extends AbstractController
{
    #[Route(
        '/user/{id}',
        name: 'index_user',
        requirements: ['id' => Requirement::DIGITS],
        methods: ['GET']
    )]
    public function index(int $id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                sprintf("Aucun utilisateur trouvé avec l'ID %d", $id)
            );
        }

        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }
}
