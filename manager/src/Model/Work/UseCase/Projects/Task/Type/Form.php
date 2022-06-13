<?php

namespace App\Model\Work\UseCase\Projects\Task\Type;

use App\Model\Work\Entity\Projects\Task\Type as TaskType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', Type\ChoiceType::class, [
            'choices' => [
                'None' => TaskType::NONE,
                'Feature' => TaskType::FEATURE,
                'Bugfix' => TaskType::BUGFIX,
                'Code review' => TaskType::CODE_REVIEW,
                'QA Test' => TaskType::CODE_REVIEW
            ], 'attr' => ['onChange' => 'this.form.submit()']
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'type';
    }
}
