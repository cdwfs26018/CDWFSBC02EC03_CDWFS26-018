<?php

namespace App\Security\Voter;

use App\Entity\Evenement;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EvenementVoter extends Voter
{
    public const EDIT = 'EVENT_EDIT';
    public const DELETE = 'EVENT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::DELETE], true)) {
            return false;
        }

        return $subject instanceof Evenement;
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token
    ): bool {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Evenement $evenement */
        $evenement = $subject;

        // Admin = full access
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        // Responsable = accès à son événement
        return $evenement->getResponsable() === $user;
    }
}
