<?php

namespace App\Model\Work\UseCase\Members\Member\Edit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', Type\EmailType::class)
            ->add('firstName', Type\TextType::class)
            ->add('lastName', Type\TextType::class);
    }
}
