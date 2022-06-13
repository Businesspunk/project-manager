<?php

namespace App\ReadModel\Work\Task\Filter;

use App\Model\Work\Entity\Projects\Task\Status;
use App\ReadModel\Work\Member\MemberFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Model\Work\Entity\Projects\Task\Type as TaskType;

class Form extends AbstractType
{
    private $members;

    public function __construct(MemberFetcher $members)
    {
        $this->members = $members;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $projectId = $options['project_id'];
        $members = [];

        $membersQuery = $this->members->activeDepartmentList($projectId);

        foreach ($membersQuery as $member) {
            $members[$member['department']][$member['name']] = $member['id'];
        }

        $defaultOptions = [
            'attr' => [
                'class' => 'd-inline',
                'onChange' => 'this.form.submit()'
            ],
            'required' => false
        ];

        $builder
            ->add('search', Type\TextType::class, array_merge_recursive(
                $defaultOptions,
                ['attr' => ['placeholder' => 'Search...']]
            ))->add('type', Type\ChoiceType::class, array_merge_recursive(
                $defaultOptions,
                [
                    'placeholder' => 'All types',
                    'choices' => [
                        'Feature' => TaskType::FEATURE,
                        'Bugfix' => TaskType::BUGFIX,
                        'Code review' => TaskType::CODE_REVIEW,
                        'None' => TaskType::NONE
                    ]
                ]
            ))->add('status', Type\ChoiceType::class, array_merge_recursive(
                $defaultOptions,
                [
                    'placeholder' => 'All statuses',
                    'choices' => [
                        'New' => Status::NEW,
                        'In work' => Status::IN_WORK,
                        'Need approve' => Status::NEED_APPROVE,
                        'Done' => Status::DONE
                    ]
                ]
            ))->add('priority', Type\ChoiceType::class, array_merge_recursive(
                $defaultOptions,
                [
                    'placeholder' => 'All priorities',
                    'choices' => [
                        'Extra' => 4,
                        'High' => 3,
                        'Normal' => 2,
                        'Low' => 1
                    ]
                ]
            ))->add('executor', Type\ChoiceType::class, array_merge_recursive(
                $defaultOptions,
                [
                    'placeholder' => 'All executors',
                    'choices' => $members
                ]
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('project_id');

        $resolver->setDefaults([
            'method' => 'get'
        ]);
    }
}
