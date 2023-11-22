<?php

namespace Nyholm\ClassRequirementExtractor\Test\Resources;

use Symfony\Component\Validator\Constraints\NotNull;

class Nullable
{
    private $noTypeHint;
    #[NotNull]
    private $noTypeHintYesAnnotation;
    private string $notNullable;
    private ?string $nullableString;
}
