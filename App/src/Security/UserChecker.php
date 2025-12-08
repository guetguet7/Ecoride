<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->isSuspended()) {
            throw new CustomUserMessageAuthenticationException('Compte suspendu. Contactez un administrateur.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Rien de plus pour le moment
    }
}
