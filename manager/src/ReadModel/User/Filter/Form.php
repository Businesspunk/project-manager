<?php

namespace App\ReadModel\User\Filter;

use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
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
            ))->add('role', Type\ChoiceType::class, array_merge_recursive(
                $defaultOptions,
                [
                    'placeholder' => 'All roles',
                    'choices' => [
                        'User' => Role::ROLE_USER,
                        'Admin' => Role::ROLE_ADMIN
                    ]
                ]
            ))->add('status', Type\ChoiceType::class, array_merge_recursive(
                    $defaultOptions,
                    [
                        'placeholder' => 'All statuses',
                        'choices' => [
                            'Active' => User::STATUS_ACTIVE,
                            'Wait' => User::STATUS_WAIT,
                            'Block' => User::STATUS_BLOCK
                        ]
                    ])
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'get'
        ]);
    }
}