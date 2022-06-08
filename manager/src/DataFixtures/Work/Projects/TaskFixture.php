<?php

namespace App\DataFixtures\Work\Projects;

use App\DataFixtures\Work\Members\MemberFixture;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Membership;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\Status;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Model\Work\Entity\Projects\Task\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixture extends Fixture implements DependentFixtureInterface
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $projects = [
            $this->getReference(ProjectFixture::REFERENCE_FIRST),
            $this->getReference(ProjectFixture::REFERENCE_SECOND)
        ];

        $previous = [];
        $date = new \DateTimeImmutable('- 1 year');

        for ($i = 1; $i <= 100; $i++) {
            /** @var Project $project */
            $project = $this->faker->randomElement($projects);
            $memberships = $project->getMemberships()->toArray();

            $task = $this->createRandomTask(
                new Id($i),
                $project,
                $date
            );
            $date = $date->modify('+' . $this->faker->numberBetween(1, 30) . 'days');

            if ($this->faker->boolean) {
                $task->plan($date->modify('+ 3 months'));
            }

            if ($this->faker->boolean) {
                $task->changeProgress($this->faker->randomElement([25,50,75]));
                $task->changeStatus(
                    new Status($this->faker->randomElement([
                        Status::IN_WORK,
                        Status::NEED_APPROVE,
                        Status::DONE
                    ])),
                    $date
                );
            }

            if ($this->faker->boolean) {
                $task->setChildOf($this->faker->randomElement($previous));
            }

            /** @var Membership $membership */
            foreach ($this->faker->randomElements($memberships, random_int(0, count($memberships))) as $membership) {
                $task->assignExecutor($membership->getMember());
            }
            $previous[] = $task;
            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixture::class
        ];
    }

    private function createRandomTask(Id $id, Project $project, \DateTimeImmutable $date): Task
    {
        return new Task(
            $id,
            $project,
            $this->faker->randomElement($project->getMemberships()->toArray())->getMember(),
            $date,
            trim($this->faker->sentence(random_int(2, 4)), '.'),
            $this->faker->paragraphs(3, true),
            $this->faker->numberBetween(1, 4),
            new Type($this->faker->randomElement([Type::NONE, Type::CODE_REVIEW, Type::BUGFIX, Type::FEATURE]))
        );
    }
}
