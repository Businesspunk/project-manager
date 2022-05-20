<?php

namespace App\Model\Work\UseCase\Projects\Project\Member\Assign;

use App\Model\Work\Entity\Projects\Project\Project;
use App\ReadModel\Work\Member\MemberFetcher;
use App\ReadModel\Work\Project\DepartmentFetcher;
use App\ReadModel\Work\Project\RoleFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $members;
    private $departments;
    private $roles;

    public function __construct(MemberFetcher $members, DepartmentFetcher $departments, RoleFetcher $roles)
    {
        $this->members = $members;
        $this->departments = $departments;
        $this->roles = $roles;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $members = [];
        foreach ($this->members->listWithGroups() as $member) {
            $members[$member['group']][$member['name']] = $member['id'];
        }

        /** @var Project $project */
        $project = $options['project'];
        $project->getDepartments()->toArray();
        $builder->add('member', Type\ChoiceType::class, [
            'placeholder' => 'Member',
            'choices' => $members
        ])->add('departments', Type\ChoiceType::class, [
            'choices' => array_flip($this->departments->assoc($project->getId())),
            'expanded' => true,
            'multiple' => true
        ])->add('roles', Type\ChoiceType::class, [
            'choices' => array_flip($this->roles->assoc()),
            'expanded' => true,
            'multiple' => true
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('project');
    }
}
