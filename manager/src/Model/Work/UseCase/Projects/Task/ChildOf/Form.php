<?php

namespace App\Model\Work\UseCase\Projects\Task\ChildOf;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use App\Model\Work\Entity\Projects\Task\Type as TaskType;
use Symfony\Component\Form\FormBuilderInterface;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('parent', Type\IntegerType::class, ['required' => false]);
    }
}
