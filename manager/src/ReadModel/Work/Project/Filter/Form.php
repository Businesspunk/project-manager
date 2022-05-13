<?php

namespace App\ReadModel\Work\Project\Filter;

use App\Model\Work\Entity\Projects\Project\Status;
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
                $defaultOptions,
                ['attr' => ['placeholder' => 'Name']]
            ))->add('status', Type\ChoiceType::class, array_merge_recursive(
                $defaultOptions,
                [
                    'placeholder' => 'All statuses',
                    'choices' => [
                        'Active' => Status::STATUS_ACTIVE,
                        'Archived' => Status::STATUS_ARCHIVED,
                    ]
                ]
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'get'
        ]);
    }
}
