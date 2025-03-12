<?php

namespace App\Security;

use App\Entity\Test;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TestVoter extends Voter
{
    public const VIEW = "view";
    public const EDIT = "edit";

    private const ATTRIBUTES = [self::VIEW, self::EDIT];

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Test &&
            in_array($attribute, self::ATTRIBUTES);
    }

    protected function voteOnAttribute(
        string $attribute,
        mixed $subject,
        TokenInterface $token,
    ): bool {
        if ($this->security->isGranted("ROLE_ADMIN")) {
            return true;
        }

        $user = $token->getUser();
        /** @var Test $test */
        $test = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($test, $user);
            case self::EDIT:
                return $this->canEdit($test, $user);
        }

        throw new \LogicException("This code should not be reached!");
    }

    private function canView(Test $test, UserInterface $user): bool
    {
        return $this->canEdit($test, $user);
    }

    private function canEdit(Test $test, UserInterface $user): bool
    {
        return
            $this->security->isGranted("ROLE_USER") &&
            $test->getUser()->getUserIdentifier() === $user->getUserIdentifier();
    }
}
