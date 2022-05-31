<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

class ErrorHandler
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(\DomainException $e)
    {
        $this->logger->warning($e->getMessage(), ['exception' => $e]);
    }
}
