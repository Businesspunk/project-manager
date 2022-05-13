<?php

namespace App\Model\User\UseCase\Role;

use App\Model\User\Entity\User\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('role', Type\ChoiceType::class, [
                'choices' => [
                    'User' => Role::ROLE_USER,
                    'Admin' => Role::ROLE_ADMIN
                ]
            ]);
    }
}
