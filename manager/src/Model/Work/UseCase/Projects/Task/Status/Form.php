<?php

namespace App\Model\Work\UseCase\Projects\Task\Status;

use App\Model\Work\Entity\Projects\Task\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('status', Type\ChoiceType::class, [
            'choices' => [
                'In work' => Status::IN_WORK,
                'Need approve' => Status::NEED_APPROVE,
                'Done' => Status::DONE
            ],
            'attr' => ['onChange' => 'this.form.submit()']
        ]);
    }
}
