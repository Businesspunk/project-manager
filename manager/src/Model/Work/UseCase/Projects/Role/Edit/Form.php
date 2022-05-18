<?php

namespace App\Model\Work\UseCase\Projects\Role\Edit;

use App\Model\Work\Entity\Projects\Role\Permission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $names = Permission::getNames();

        $builder
            ->add('name', Type\TextType::class)
            ->add('permissions', Type\ChoiceType::class, [
                'choices' => array_combine($names, $names),
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'translation_domain' => 'work_permissions'
            ]);
    }
}
