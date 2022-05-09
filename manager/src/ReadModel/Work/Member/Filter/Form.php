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
        $defaultOptions = [
            'attr' => [
                'class' => 'd-inline',
                'onChange' => 'this.form.submit()'
            ],
            'required' => false
        ];

        $builder
            ->add('name', Type\TextType::class, array_merge_recursive(
                $defaultOptions, ['attr' => ['placeholder' => 'Name']]
            ))->add('email', Type\TextType::class, array_merge_recursive(
                $defaultOptions, ['attr' => ['placeholder' => 'E-mail']]
            ))->add('group', Type\ChoiceType::class, array_merge_recursive(
                $defaultOptions, [
                    'placeholder' => 'All groups',
                    'choices' => array_flip($this->groups->assoc()),
                ]
            ))->add('status', Type\ChoiceType::class, array_merge_recursive(
                $defaultOptions, [
                    'placeholder' => 'All statuses',
                    'choices' => [
                        'Active' => Status::STATUS_ACTIVE,
                        'Archived' => Status::STATUS_ARCHIVED,
                    ]
                ]
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'get'
        ]);
    }
}