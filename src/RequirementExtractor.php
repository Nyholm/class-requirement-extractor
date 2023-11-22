<?php

namespace Nyholm\ClassRequirementExtractor;

use Nyholm\ClassRequirementExtractor\Model\CollectionRequirement;
use Nyholm\ClassRequirementExtractor\Model\Requirement;
use Nyholm\ClassRequirementExtractor\Model\RequirementList;
use Nyholm\ClassRequirementExtractor\Model\RequirementMap;
use phpDocumentor\Reflection\DocBlock;
use Symfony\Component\PropertyInfo\PropertyAccessExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Constraints\Type;

class RequirementExtractor
{
    private array $attributeProcessors = [];

    public function __construct(
        private PropertyAccessExtractorInterface $propertyAccessExtractor,
        private PropertyTypeExtractorInterface $propertyTypeExtractor,
        private DocBlockParser $docBlockParser,
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
     * @return array<string, Requirement>
     */
    public function extract(string $class): array
    {
        $properties = $this->getAllProperties($class);

        $requirements = [];
        foreach ($properties as $property) {
            $propertyName = $property->name;
            $requirementClass = $this->getRequirementType($class, $property);

            /** @var Requirement|RequirementList|RequirementMap $requirement */
            $requirement = new $requirementClass(
                $propertyName,
                $this->propertyAccessExtractor->isWritable($class, $propertyName) ?? false,
                $this->propertyAccessExtractor->isReadable($class, $propertyName) ?? false
            );
            $requirements[$propertyName] = $requirement;

            $this->parseTypes($class, $propertyName, $requirement);

            $attributes = $property->getAttributes();
            foreach ($attributes as $attribute) {
                $this->parseAttribute($requirement, $attribute->newInstance());
            }

            $docBlock = $this->docBlockParser->getDocBlock($property->class, $propertyName)[0];
            if (null !== $docBlock) {
                $this->parseDocBlock($requirement, $docBlock);
            }

            if ($requirement instanceof CollectionRequirement) {
                $types = array_filter($requirement->getTypes(), fn ($type) => class_exists($type));
                if (count($types) > 1) {
                    throw new \LogicException('This is not yet supported');
                }
                foreach ($types as $type) {
                    $requirement->setChildRequirements($this->extract($type));
                }
            }
        }

        return $requirements;
    }

    /**
     * This method will figure out if we need a Requirement, RequirementList or RequirementMap.
     *
     * @param class-string $class
     *
     * @return class-string
     */
    private function getRequirementType(string $class, \ReflectionProperty $property): string
    {
        $stringTypes = [];
        foreach ($this->propertyTypeExtractor->getTypes($class, $property->getName()) ?? [] as $type) {
            $stringTypes[] = $type->getBuiltinType();
        }
        foreach ($property->getAttributes(Type::class) as $attribute) {
            $instance = $attribute->newInstance();

            foreach ((array) $instance->type as $type) {
                $stringTypes[] = $type;
            }
        }

        if (in_array('object', $stringTypes)) {
            return RequirementMap::class;
        }

        // If type is array, or attribute All or Collection, then do List
        if (in_array('array', $stringTypes) || [] !== $property->getAttributes(All::class) || [] !== $property->getAttributes(Collection::class)) {
            return RequirementList::class;
        }

        return Requirement::class;
    }

    private function parseAttribute(Requirement $requirement, object $attribute): void
    {
        if ($attribute instanceof Sequentially) {
            foreach ($attribute->getNestedConstraints() as $constraint) {
                $this->parseAttribute($requirement, $constraint);
            }

            return;
        }

        if ($attribute instanceof Collection) {
            // TODO treat "fields" as an array.
            // @see https://symfony.com/doc/current/reference/constraints/Collection.html
        }

        if ($attribute instanceof All) {
            if (!$requirement instanceof RequirementList) {
                throw new \LogicException('We must use a RequirementList if attribute All exists');
            }
            $child = new Requirement('', $requirement->isWriteable(), $requirement->isReadable());
            $requirement->setChildRequirements([$child]);
            foreach ($attribute->getNestedConstraints() as $constraint) {
                $this->parseAttribute($child, $constraint);
            }

            return;
        }

        $name = get_class($attribute);
        foreach (['*', $name] as $key) {
            /** @var AttributeProcessorInterface $processor */
            foreach ($this->attributeProcessors[$key] ?? [] as $processor) {
                $processor->handle($requirement, $attribute);
            }
        }
    }

    /**
     * @param class-string $class
     *
     * @return \ReflectionProperty[]
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

        return $properties;
    }

    private function parseDocBlock(Requirement $requirement, DocBlock $docBlock): void
    {
        foreach ($docBlock->getTags() as $tag) {
            if ('example' === $tag->getName()) {
                $requirement->addExample((string) $tag);
            } elseif ('deprecated' === $tag->getName()) {
                $requirement->setDeprecated(true);
            }
        }
    }

    /**
     * @param class-string $class
     */
    private function parseTypes(string $class, string $propertyName, Requirement $requirement): void
    {
        $types = $this->propertyTypeExtractor->getTypes($class, $propertyName) ?? [];
        if ([] === $types) {
            $requirement->setNullable(true);

            return;
        }

        $childTypes = [];
        foreach ($types as $type) {
            $requirement->setNullable($requirement->isNullable() || $type->isNullable());
            if ($requirement instanceof RequirementList && $type->isCollection()) {
                $childTypes[] = $type->getCollectionValueTypes();
                continue;
            }

            $typeString = $type->getClassName();
            if (null === $typeString) {
                $typeString = $type->getBuiltinType();
            }

            $requirement->addType($typeString);
        }

        foreach ([] !== $childTypes ? array_merge(...$childTypes) : [] as $type) {
            $typeString = $type->getClassName();
            if (null === $typeString) {
                $typeString = $type->getBuiltinType();
            }

            $requirement->addType($typeString);
        }
    }
}
