<?php

namespace App\Widget\Task;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TypeWidget extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('task_type', [$this, 'type'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function type(Environment $twig, string $type)
    {
        return $twig->render('app/widget/work/projects/task/type.html.twig', compact('type'));
    }
}
