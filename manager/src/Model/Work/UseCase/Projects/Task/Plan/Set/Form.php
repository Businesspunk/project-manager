<?php

namespace App\Model\Work\UseCase\Projects\Task\Plan\Set;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use App\Model\Work\Entity\Projects\Task\Type as TaskType;
use Symfony\Component\Form\FormBuilderInterface;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'plan',
            Type\DateType::class,
            [
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'input'  => 'datetime_immutable',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker']
            ]
        );
    }
}
