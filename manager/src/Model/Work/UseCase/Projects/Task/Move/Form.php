<?php

namespace App\Model\Work\UseCase\Projects\Task\Move;

use App\ReadModel\Work\Project\ProjectFetcher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class Form extends AbstractType
{
    private $projects;

    public function __construct(ProjectFetcher $projects)
    {
        $this->projects = $projects;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('project', Type\ChoiceType::class, [
                'choices' => array_column($this->projects->listAll(), 'id', 'name')
            ])
            ->add('withChildren', Type\CheckboxType::class, ['required' => false]);
    }
}
