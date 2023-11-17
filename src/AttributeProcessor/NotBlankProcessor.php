<?php

namespace Nyholm\ClassRequirementExtractor\AttributeProcessor;

use Nyholm\ClassRequirementExtractor\AttributeProcessorInterface;
use Nyholm\ClassRequirementExtractor\Requirement;
use Symfony\Component\Validator\Constraints\NotBlank;

class NotBlankProcessor implements AttributeProcessorInterface
{
    public function handle(Requirement $requirement, \ReflectionAttribute $attribute)
    {
        $arguments = $attribute->getArguments();
        $object = new NotBlank(...$arguments);
        $requirement->setAllowEmptyValue(false);
        $requirement->setNullable($object->allowNull);
    }

    public function supportedAttributes(): array
    {
        return [NotBlank::class];
    }

}