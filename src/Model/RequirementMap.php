<?php

namespace Nyholm\ClassRequirementExtractor\Model;

/**
 * This represent Foo::$bar where $bar is an instance of Bar.
 *
 * There the RequirementList::getRequirements() will return the requirements for Bar.
 */
class RequirementMap extends Requirement implements CollectionRequirement
{
    use CollectionRequirementTrait;
}
