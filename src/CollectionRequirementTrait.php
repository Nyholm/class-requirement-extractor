<?php

namespace Nyholm\ClassRequirementExtractor;

trait CollectionRequirementTrait
{
    private array $childRequirements = [];

    /**
     * @return array<string, Requirement>
     */
    public function getRequirements(): array
    {
        return $this->childRequirements;
    }

    public function setChildRequirements(array $childRequirements): void
    {
        $this->childRequirements = $childRequirements;
    }
}
