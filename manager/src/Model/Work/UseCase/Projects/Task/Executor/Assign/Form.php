<?php

namespace App\Model\Work\UseCase\Projects\Task\Executor\Assign;

use App\ReadModel\Work\Member\MemberFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $members;

    public function __construct(MemberFetcher $members)
    {
        $this->members = $members;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $members = [];
        $memberQuery = $this->members->activeDepartmentList($options['project_id']) ?? [];
        foreach ($memberQuery as $member) {
            $members[$member['department'] . ' - ' . $member['name'] . ' (' . $member['email'] . ')'] = $member['id'];
        }

        $builder->add('members', Type\ChoiceType::class, [
            'choices' => $members,
            'expanded' => true,
            'multiple' => true
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('project_id');
    }
}
