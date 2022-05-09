<?php

namespace App\ReadModel\Work\Member\Filter;

use App\Model\Work\Entity\Members\Member\Status;
use App\ReadModel\Work\Group\GroupFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $groups;

    public function __construct(GroupFetcher $groups)
    {
        $this->groups = $groups;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Type\TextType::class,
                [
                    'attr'=> [
                        'class' => 'd-inline',
                        'placeholder' => 'Name',
                        'onChange' => 'this.form.submit()'
                    ],
                    'required' => false,
                ]
            )
            ->add('email', Type\TextType::class,
                [
                    'attr'=> [
                        'class' => 'd-inline',
                        'placeholder' => 'Email',
                        'onChange' => 'this.form.submit()'
                    ],
                    'required' => false
                ]
            )
            ->add('group', Type\ChoiceType::class, [
                'placeholder' => 'All groups',
                'choices' => array_flip($this->groups->assoc()),
                'attr'=> [
                    'class' => 'd-inline',
                    'onChange' => 'this.form.submit()'
                ],
                'required' => false
            ])
            ->add('status', Type\ChoiceType::class, [
                'placeholder' => 'All statuses',
                'choices' => [
                    'Active' => Status::STATUS_ACTIVE,
                    'Archived' => Status::STATUS_ARCHIVED,
                ],
                'attr'=> [
                    'class' => 'd-inline',
                    'onChange' => 'this.form.submit()'
                ],
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'get'
        ]);
    }
}