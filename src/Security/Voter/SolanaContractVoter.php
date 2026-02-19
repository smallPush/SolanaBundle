<?php

namespace App\Security\Voter;

use App\Entity\SolanaContract;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SolanaContractVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const VALIDATE = 'VALIDATE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::VALIDATE])
            && $subject instanceof SolanaContract;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var SolanaContract $contract */
        $contract = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($contract, $user),
            self::VALIDATE => $this->canValidate($contract, $user),
            default => false,
        };
    }

    private function canView(SolanaContract $contract, User $user): bool
    {
        return $user === $contract->getAuthor()
            || $user === $contract->getDonor()
            || $user === $contract->getVolunteer();
    }

    private function canValidate(SolanaContract $contract, User $user): bool
    {
        return $user === $contract->getDonor()
            || $user === $contract->getVolunteer();
    }
}
