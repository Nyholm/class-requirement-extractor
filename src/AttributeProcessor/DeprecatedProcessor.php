<?php

namespace Nyholm\ClassRequirementExtractor\AttributeProcessor;

use Nyholm\ClassRequirementExtractor\AttributeProcessorInterface;
use Nyholm\ClassRequirementExtractor\Requirement;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeprecatedProcessor implements AttributeProcessorInterface
{
    public function handle(Requirement $requirement, object $attribute)
    {
        $requirement->setDeprecated(true);
    }

    public function supportedAttributes(): array
    {
        return [\Deprecated::class];
    }
}
