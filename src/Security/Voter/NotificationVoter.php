<?php
// src/Security/Voter/NotificationVoter.php
namespace App\Security\Voter;

use App\Entity\Notification;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class NotificationVoter extends Voter
{
    public const VIEW      = 'VIEW';
    public const MARK_READ = 'MARK_READ';   // alias pour PATCH /read
    public const DELETE    = 'DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        return
            $subject instanceof Notification &&
            \in_array($attribute, [self::VIEW, self::MARK_READ, self::DELETE], true);
    }

    /**
     * @param Notification $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user) {
            return false;          
        }

        return $subject->getUser() === $user;
    }
}
