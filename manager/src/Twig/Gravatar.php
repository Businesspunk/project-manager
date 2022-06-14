<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Gravatar extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('gravatar', [$this, 'getUrl']),
        ];
    }

    public function getUrl(string $email, int $size = 80): string
    {
        return sprintf('https://www.gravatar.com/avatar/%s?%s', md5(strtolower(trim($email))), http_build_query([
            's' => $size,
            'd' => 'identicon'
        ]));
    }
}
