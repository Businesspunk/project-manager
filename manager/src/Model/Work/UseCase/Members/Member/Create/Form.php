<?php

namespace App\Model\Work\UseCase\Members\Member\Create;

use App\ReadModel\Work\Group\GroupFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class Form extends AbstractType
{
    private $groups;

    public function __construct(GroupFetcher $groups)
    {
        $this->groups = $groups;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('group', Type\ChoiceType::class, [
                'placeholder' => 'Group',
                'choices' => array_flip($this->groups->assoc())
            ])
            ->add('email', Type\EmailType::class)
            ->add('firstName', Type\TextType::class)
            ->add('lastName', Type\TextType::class);
    }
}
