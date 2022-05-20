<?php

namespace App\Security\Voter\Work;

use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Role\Permission;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class ProjectAccess extends Voter
{
    public const CREATE = 'create';
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const MANAGE_MEMBERS = 'manage_members';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        $allowed = [self::CREATE, self::VIEW, self::EDIT, self::MANAGE_MEMBERS];
        return ($subject instanceof Project || $subject === Project::class)
                && in_array($attribute, $allowed);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserIdentity) {
            return false;
        }

        /** @var Project $project */
        $project = $subject;

        switch ($attribute) {
            case self::EDIT:
            case self::CREATE:
                return $this->security->isGranted('ROLE_WORK_PROJECTS_MANAGE');
            case self::VIEW:
                return $this->security->isGranted('ROLE_WORK_PROJECTS_MANAGE')
                    || $project->hasMember(new Id($user->getId()));
            case self::MANAGE_MEMBERS:
                return $this->security->isGranted('ROLE_WORK_PROJECTS_MANAGE')
                    || $project->isMemberGranted(new Id($user->getId()), Permission::MANAGE_PROJECT_MEMBERS);
        }

        return false;
    }
}
