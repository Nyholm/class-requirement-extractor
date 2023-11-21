<?php

namespace Nyholm\ClassRequirementExtractor;

interface CollectionRequirement
{
    /**
     * @return array<string, Requirement>
     */
    public function getRequirements(): array;
}
