<?php

namespace App\Model\Work\UseCase\Members\Member\Move;

use App\ReadModel\Work\Group\GroupFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    private $groups;

    public function __construct(GroupFetcher $groups)
    {
        $this->groups = $groups;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $groups = $this->groups->assoc();
        unset($groups[$options['currentGroup']]);

        $builder->add('group', Type\ChoiceType::class, [
                'placeholder' => 'Group',
                'choices' => array_flip($groups)
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('currentGroup');
    }
}