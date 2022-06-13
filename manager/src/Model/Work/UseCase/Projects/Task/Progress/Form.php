<?php

namespace App\Model\Work\UseCase\Projects\Task\Progress;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('progress', Type\ChoiceType::class, [
            'choices' => [
                0 => 0,
                25 => 25,
                50 => 50,
                75 => 75,
                100 => 100
            ],
            'attr' => ['onChange' => 'this.form.submit()']
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'progress';
    }
}
