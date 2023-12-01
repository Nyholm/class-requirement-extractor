<?php

namespace Nyholm\ClassRequirementExtractor;

use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;

/**
 * An aggregator over many PropertyTypeExtractorInterface and will return all Types.
 */
class AllTypeExtractor implements PropertyTypeExtractorInterface
{
    /**
     * @param iterable<mixed, PropertyTypeExtractorInterface> $typeExtractors
     */
    public function __construct(
        private iterable $typeExtractors
    ) {
    }

    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        $output = [];
        foreach ($this->typeExtractors as $extractor) {
            if (null !== $value = $extractor->getTypes($class, $property, $context)) {
                $output[] = $value;
            }
        }

        return [] !== $output ? array_merge(...$output) : [];
    }
}
