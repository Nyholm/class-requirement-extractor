<?php

namespace Nyholm\ClassRequirementExtractor;

use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Webmozart\Assert\Assert;

class RequirementExtractor
{
    private array $attributeProcessors = [];
    public function __construct(
        private PropertyInfoExtractorInterface $propertyExtractor,
        iterable $attributeProcessors
    ) {
        foreach ($attributeProcessors as $processor) {
            $this->addAttributeProcessor($processor);
        }
    }

    public function addAttributeProcessor(AttributeProcessorInterface $processor): void
    {
        $supported = $processor->supportedAttributes();
        foreach ($supported as $type) {
            if (!isset($this->attributeProcessors[$type])) {
                $this->attributeProcessors[$type] = [];
            }

            $this->attributeProcessors[$type][] = $processor;
        }
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
                $requirement->setNullable($type->isNullable());
                $typeString = $type->getClassName();
                if (null === $typeString) {
                    $typeString = $type->getBuiltinType();
                }

                $requirement->addType($typeString);
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
        foreach (['*', $name] as $key) {
            /** @var AttributeProcessorInterface $processor */
            foreach ($this->attributeProcessors[$key] ?? [] as $processor) {
                $processor->handle($requirement, $attribute);
            }
        }
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