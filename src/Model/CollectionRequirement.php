<?php

namespace Nyholm\ClassRequirementExtractor\Model;

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

    public function getTypes(): array;
}
