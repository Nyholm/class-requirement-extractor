<?php

namespace Nyholm\ClassRequirementExtractor\AttributeProcessor;

use Nyholm\ClassRequirementExtractor\AttributeProcessorInterface;
use Nyholm\ClassRequirementExtractor\Model\Requirement;
use Symfony\Component\Validator\Constraints\NotBlank;

class NotBlankProcessor implements AttributeProcessorInterface
{
    public function handle(Requirement $requirement, object $attribute)
    {
        $requirement->setAllowEmptyValue(false);
        $requirement->setNullable($attribute->allowNull);
    }

    public function supportedAttributes(): array
    {
        return [NotBlank::class];
    }
}
