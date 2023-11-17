<?php

namespace Nyholm\ClassRequirementExtractor;

interface AttributeProcessorInterface
{
    public function handle(Requirement $requirement, object $attribute);

    /**
     * Return a list of attribute classes that we would like to process.
     *
     * @return string[]
     */
    public function supportedAttributes(): array;
}
