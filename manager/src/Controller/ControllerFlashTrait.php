<?php

namespace App\Controller;

trait ControllerFlashTrait
{
    private function addExceptionFlash(\DomainException $e): void
    {
        $this->addFlash('error', $this->translator->trans($e->getMessage(), [], 'exceptions'));
    }
}
