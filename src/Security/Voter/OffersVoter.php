<?php

namespace App\Security\Voter;

use App\Entity\Annonces;
use App\Entity\Users;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class OffersVoter extends Voter
{
    const OFFER_EDIT = 'offer_edit';
    const OFFER_DELETE = 'offer_delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    private function canEdit(Annonces $offer, Users $user)
    {
        return $user === $offer->getUsers();
    }

    private function canDelete()
    {
        if ($this->security->isGranted('ROLE_EDITOR')) return true;
        return false;
    }

    protected function supports(string $attribute, $offer): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::OFFER_EDIT, self::OFFER_DELETE])
            && $offer instanceof \App\Entity\Annonces;
    }

    protected function voteOnAttribute(string $attribute, $offer, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Vérifie si l'utilisateur est 'admin'
        if ($this->security->isGranted('ROLE_ADMIN')) return true;

        // Vérifie si l'annonce a un propriétaire
        if (null === $offer->getUsers()) return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::OFFER_EDIT:
                // Vérifie si on peut éditer
                return $this->canEdit($offer, $user);
                break;
            case self::OFFER_DELETE:
                // Vérifie si on peut supprimer
                return $this->canDelete();
                break;
        }

        return false;
    }
}
