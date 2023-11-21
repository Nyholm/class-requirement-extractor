<?php

namespace Nyholm\ClassRequirementExtractor;

interface CollectionRequirement
{
    /**
     * @return array<string, Requirement>
     */
    public function getRequirements(): array;

    /**
     * @param array<string, Requirement> $childRequirements
     */
    public function setChildRequirements(array $childRequirements): void;
}
