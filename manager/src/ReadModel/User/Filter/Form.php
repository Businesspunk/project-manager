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
            ->add('role', Type\ChoiceType::class, [
                'placeholder' => 'All roles',
                'choices' => [
                    'User' => Role::ROLE_USER,
                    'Admin' => Role::ROLE_ADMIN
                ],
                'attr'=> [
                    'class' => 'd-inline',
                    'onChange' => 'this.form.submit()'
                ],
                'required' => false
            ])
            ->add('status', Type\ChoiceType::class, [
                'placeholder' => 'All statuses',
                'choices' => [
                    'Active' => User::STATUS_ACTIVE,
                    'Wait' => User::STATUS_WAIT,
                    'Block' => User::STATUS_BLOCK
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