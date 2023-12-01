<?php

namespace Nyholm\ClassRequirementExtractor\Model;

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

    public function getChildTypes(): array
    {
        return parent::getTypes();
    }
}
