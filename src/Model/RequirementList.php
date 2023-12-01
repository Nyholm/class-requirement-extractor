<?php

namespace Nyholm\ClassRequirementExtractor\Model;

/**
 * This represent Foo::$bar where $bar is an array of Bar.
 *
 * There the RequirementList::getRequirements() will return the requirements for Bar.
 */
class RequirementList extends Requirement implements CollectionRequirement
{
    use CollectionRequirementTrait;

    public function getTypes(): array
    {
        return ['array'];
    }
}
