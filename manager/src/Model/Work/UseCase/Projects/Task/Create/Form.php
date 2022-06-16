<?php

namespace App\Model\Work\UseCase\Projects\Task\Create;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use App\Model\Work\Entity\Projects\Task\Type as TaskType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titles', TitlesType::class)
            ->add('content', Type\TextareaType::class, ['attr' => ['rows' => 10], 'required' => false])
            ->add('type', Type\ChoiceType::class, [
                'choices' => [
                    'None' => TaskType::NONE,
                    'Feature' => TaskType::FEATURE,
                    'Bugfix' => TaskType::BUGFIX,
                    'Code review' => TaskType::CODE_REVIEW,
                    'QA Test' => TaskType::CODE_REVIEW
                ]
            ])
            ->add('priority', Type\ChoiceType::class, [
                'choices' => [
                    'Low' => 1,
                    'Normal' => 2,
                    'High' => 3,
                    'Extra' => 4
                ]
            ])
            ->add(
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

        if ($options['parentId']) {
            $builder->add('parent', Type\HiddenType::class);
        } else {
            $builder->add('parent', Type\IntegerType::class, ['required' => false]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('parentId');
    }
}
