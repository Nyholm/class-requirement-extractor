<?php

namespace AttributeProcessor;

use Nyholm\ClassRequirementExtractor\AttributeProcessorInterface;
use Nyholm\ClassRequirementExtractor\Requirement;
use Symfony\Component\Validator\Constraints\NotNull;

class NotNullProcessor implements AttributeProcessorInterface
{
    public function handle(Requirement $requirement, object $attribute)
    {
        $requirement->setAllowEmptyValue(false);
        $requirement->setNullable(false);
    }

    public function supportedAttributes(): array
    {
        return [NotNull::class];
    }
}
