<?php
// src/Security/Voter/SavedSearchVoter.php
namespace App\Security\Voter;

use App\Entity\SavedSearch;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SavedSearchVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['VIEW', 'DELETE'])
            && $subject instanceof SavedSearch;
    }

    protected function voteOnAttribute(string $attribute, $search, TokenInterface $token): bool
    {
        return $search->getOwner() === $token->getUser();
    }
}
