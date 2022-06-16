<?php

namespace App\Model\Work\UseCase\Projects\Task\Create;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class TitlesType extends AbstractType implements DataTransformerInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this);
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function transform($value): string
    {
        return '';
    }

    public function reverseTransform($value): array
    {
        return array_map(static function ($v) {
            return new TitleRow(rtrim($v));
        }, explode(PHP_EOL, $value));
    }
}
