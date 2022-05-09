<?php

namespace App\Widget\Member;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusWidget extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('member_status', [$this, 'status'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function status(Environment $twig, string $status)
    {
        return $twig->render('app/widget/work/members/member/status.html.twig', compact('status'));
    }
}