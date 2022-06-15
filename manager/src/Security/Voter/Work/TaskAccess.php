<?php

namespace App\Security\Voter\Work;

use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Projects\Role\Permission;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class TaskAccess extends Voter
{
    public const VIEW = 'view';
    public const MANAGE = 'manage';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        $allowed = [self::VIEW, self::MANAGE];
        return ($subject instanceof Task || $subject === Task::class)
                && in_array($attribute, $allowed);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserIdentity) {
            return false;
        }

        /** @var Task $task */
        $task = $subject;
        $memberId = new Id($user->getId());

        switch ($attribute) {
            case self::VIEW:
                return $this->security->isGranted('ROLE_WORK_TASK_MANAGE')
                    || $task->getProject()->isMemberGranted($memberId, Permission::VIEW_TASKS);
            case self::MANAGE:
                return $this->security->isGranted('ROLE_WORK_TASK_MANAGE')
                    || $task->getProject()->isMemberGranted($memberId, Permission::MANAGE_TASKS);
        }

        return false;
    }
}
