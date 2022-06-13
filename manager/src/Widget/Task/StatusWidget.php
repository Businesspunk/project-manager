<?php

namespace App\Widget\Task;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusWidget extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('task_status', [$this, 'status'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function status(Environment $twig, string $status)
    {
        return $twig->render('app/widget/work/projects/task/status.html.twig', compact('status'));
    }
}
