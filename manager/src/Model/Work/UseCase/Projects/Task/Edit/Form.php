<?php

namespace App\Model\Work\UseCase\Projects\Task\Edit;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use App\Model\Work\Entity\Projects\Task\Type as TaskType;
use Symfony\Component\Form\FormBuilderInterface;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', Type\TextType::class)
            ->add('content', Type\TextareaType::class, ['attr' => ['rows' => 10]]);
    }
}
