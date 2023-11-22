<?php

namespace Nyholm\ClassRequirementExtractor\AttributeProcessor;

use Nyholm\ClassRequirementExtractor\AttributeProcessorInterface;
use Nyholm\ClassRequirementExtractor\Model\Requirement;
use Symfony\Component\Validator\Constraints\Type;

class TypeProcessor implements AttributeProcessorInterface
{
    public function handle(Requirement $requirement, object $attribute)
    {
        $types = (array) $attribute->type;
        foreach ($types as $type) {
            $requirement->addType($type);
        }
    }

    public function supportedAttributes(): array
    {
        return [Type::class];
    }
}
