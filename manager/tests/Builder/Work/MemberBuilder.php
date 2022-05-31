<?php

namespace App\Tests\Builder\Work;

use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Member\Email;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\Name;
use App\Model\Work\Entity\Members\Member\Status;

class MemberBuilder
{
    private $id;
    private $group;
    private $name;
    private $email;
    private $status;

    public function __construct(
        Id $id = null,
        Group $group = null,
        Name $name = null,
        Email $email = null,
        Status $status = null
    ) {
        $this->id = $id ?? Id::next();
        $this->group = $group ?? (new GroupBuilder())->build();
        $this->name = $name ?? new Name('First', 'Second');
        $this->email = $email ?? new Email('email@gmail.com');
        $this->status = $status ?? Status::active();
    }

    public function build(): Member
    {
        return new Member(
            $this->id,
            $this->group,
            $this->name,
            $this->email,
            $this->status
        );
    }
}
