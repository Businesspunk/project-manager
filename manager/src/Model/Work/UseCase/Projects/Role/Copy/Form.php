<?php

namespace App\Model\Work\UseCase\Projects\Role\Copy;

use App\Model\Work\Entity\Projects\Role\Permission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', Type\TextType::class);
    }
}