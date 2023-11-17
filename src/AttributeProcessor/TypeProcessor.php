<?php

namespace Nyholm\ClassRequirementExtractor\AttributeProcessor;

use Nyholm\ClassRequirementExtractor\AttributeProcessorInterface;
use Nyholm\ClassRequirementExtractor\Requirement;
use Symfony\Component\Validator\Constraints\Type;

class TypeProcessor implements AttributeProcessorInterface
{
    public function handle(Requirement $requirement, \ReflectionAttribute $attribute)
    {
        $arguments = $attribute->getArguments();
        $object = new Type(...$arguments);
        $requirement->addType($object->type);
    }

    public function supportedAttributes(): array
    {
        return [Type::class];
    }
}
