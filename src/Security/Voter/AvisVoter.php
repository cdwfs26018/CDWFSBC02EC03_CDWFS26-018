<?php

namespace App\Security\Voter;

use App\Entity\Avis;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AvisVoter extends Voter
{
    public const MODERATE = 'AVIS_MODERATE';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::MODERATE && $subject instanceof Avis;
    }

    protected function voteOnAttribute(string $attribute, $avis, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        return $avis->getEvenement()->getResponsable() === $user;
    }
}
