<?php

namespace App\Widget\Task;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ExecutorsWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'task_executors',
                [$this, 'executors'],
                ['needs_environment' => true, 'is_safe' => ['html']]
            )
        ];
    }

    public function executors(Environment $twig, array $executors): string
    {
        return $twig->render('app/widget/work/projects/task/executors.html.twig', compact('executors'));
    }
}
