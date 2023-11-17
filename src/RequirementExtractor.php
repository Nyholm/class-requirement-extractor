<?php

namespace Nyholm\ClassRequirementExtractor;

use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Webmozart\Assert\Assert;

class RequirementExtractor
{
    public function __construct(
        private PropertyInfoExtractorInterface $propertyExtractor,
    ) {
    }

    /**
     * @param class-string $class
     *
     * @return Requirement[]
     */
    public function extract(string $class): array
    {
        $properties = $this->getAllProperties($class);

        $requirements = [];
        foreach ($properties as $property) {
            $requirements[$property] = $requirement = new Requirement(
                $property,
                $this->propertyExtractor->isWritable($class, $property),
                $this->propertyExtractor->isReadable($class, $property)
            );

            foreach ($this->propertyExtractor->getTypes($class, $property) as $type) {
                $x = 2;
            }

            $attributes = (new \ReflectionProperty($class, $property))->getAttributes();
            foreach ($attributes as $attribute) {
                $this->parseAttribute($requirement, $attribute);
            }
        }

        return $requirements;
    }

    private function parseAttribute(Requirement $requirement, \ReflectionAttribute $attribute)
    {
        $name = $attribute->getName();
        $arguments = $attribute->getArguments();
        $x = 2;
    }

    /**
     * @param string $class
     * @return string[]
     */
    private function getAllProperties(string $class): array
    {
        $refl = new \ReflectionClass($class);
        $properties = $refl->getProperties();

        // check parents
        while (false !== $parent = get_parent_class($class)) {
            $parentRefl = new \ReflectionClass($parent);
            $properties = array_merge($properties, $parentRefl->getProperties());
            $class = $parent;
        }

        return array_map(function ($reflectionProperty) {
            return $reflectionProperty->name;
        }, $properties);
    }
}
