<?php

namespace App\Model\Work\UseCase\Projects\Project\Member\Assign;

use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\Membership;
use App\Model\Work\Entity\Projects\Project\ProjectRepository;
use App\Model\Work\Entity\Projects\Department\Id as DepartmentId;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Projects\Role\Id as RoleId;
use App\Model\Work\Entity\Projects\Role\RoleRepository;
use App\Model\Work\Flusher;

class Handler
{
    private $projects;
    private $members;
    private $roles;
    private $flusher;

    public function __construct(
        ProjectRepository $projects,
        MemberRepository $members,
        RoleRepository $roles,
        Flusher $flusher
    ) {
        $this->projects = $projects;
        $this->members = $members;
        $this->roles = $roles;
        $this->flusher = $flusher;
    }

    public function handle(Command $command)
    {
        $member = $this->members->get(new MemberId($command->member));
        $project = $this->projects->get(new Id($command->project));

        /** @var Membership $membership */
        foreach ($project->getMemberships() as $membership) {
            if ($membership->hasMember($member)) {
                throw new \DomainException('Member attached to this project');
            }
        }

        $departmentIds = array_map(static function ($departmentId) {
            return new DepartmentId($departmentId);
        }, $command->departments);

        $roles = array_map(function ($roleId) {
            return $this->roles->get(new RoleId($roleId));
        }, $command->roles);

        $project->addMember($member, $departmentIds, $roles);
        $this->flusher->flush();
    }
}
