<?php

namespace Nyholm\ClassRequirementExtractor;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;

/**
 * This is a copy from DocBlockExtractor in Symfony/property-info.
 */
class DocBlockParser
{
    public const PROPERTY = 0;
    public const ACCESSOR = 1;
    public const MUTATOR = 2;

    /**
     * @var array<string, array{DocBlock|null, int|null, string|null}>
     */
    private $docBlocks = [];

    /**
     * @var Context[]
     */
    private $contexts = [];

    private $docBlockFactory;
    private $contextFactory;
    private $mutatorPrefixes;
    private $accessorPrefixes;

    /**
     * @param string[]|null $mutatorPrefixes
     * @param string[]|null $accessorPrefixes
     */
    public function __construct(DocBlockFactoryInterface $docBlockFactory = null, array $mutatorPrefixes = null, array $accessorPrefixes = null)
    {
        if (!class_exists(DocBlockFactory::class)) {
            throw new \LogicException(sprintf('Unable to use the "%s" class as the "phpdocumentor/reflection-docblock" package is not installed. Try running composer require "phpdocumentor/reflection-docblock".', __CLASS__));
        }

        $this->docBlockFactory = $docBlockFactory ?: DocBlockFactory::createInstance();
        $this->contextFactory = new ContextFactory();
        $this->mutatorPrefixes = $mutatorPrefixes ?? ReflectionExtractor::$defaultMutatorPrefixes;
        $this->accessorPrefixes = $accessorPrefixes ?? ReflectionExtractor::$defaultAccessorPrefixes;
    }

    private function getDocBlockFromConstructor(string $class, string $property): ?DocBlock
    {
        try {
            $reflectionClass = new \ReflectionClass($class);
        } catch (\ReflectionException) {
            return null;
        }
        $reflectionConstructor = $reflectionClass->getConstructor();
        if (!$reflectionConstructor) {
            return null;
        }

        try {
            $docBlock = $this->docBlockFactory->create($reflectionConstructor, $this->contextFactory->createFromReflector($reflectionConstructor));

            return $this->filterDocBlockParams($docBlock, $property);
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    private function filterDocBlockParams(DocBlock $docBlock, string $allowedParam): DocBlock
    {
        $tags = array_values(array_filter($docBlock->getTagsByName('param'), fn ($tag) => $tag instanceof DocBlock\Tags\Param && $allowedParam === $tag->getVariableName()));

        return new DocBlock($docBlock->getSummary(), $docBlock->getDescription(), $tags, $docBlock->getContext(),
            $docBlock->getLocation(), $docBlock->isTemplateStart(), $docBlock->isTemplateEnd());
    }

    /**
     * @return array{DocBlock|null, int|null, string|null}
     */
    public function getDocBlock(string $class, string $property): array
    {
        $propertyHash = sprintf('%s::%s', $class, $property);

        if (isset($this->docBlocks[$propertyHash])) {
            return $this->docBlocks[$propertyHash];
        }

        try {
            $reflectionProperty = new \ReflectionProperty($class, $property);
        } catch (\ReflectionException) {
            $reflectionProperty = null;
        }

        $ucFirstProperty = ucfirst($property);

        switch (true) {
            case $reflectionProperty?->isPromoted() && $docBlock = $this->getDocBlockFromConstructor($class, $property):
                $data = [$docBlock, self::MUTATOR, null];
                break;

            case $docBlock = $this->getDocBlockFromProperty($class, $property):
                $data = [$docBlock, self::PROPERTY, null];
                break;

            case [$docBlock] = $this->getDocBlockFromMethod($class, $ucFirstProperty, self::ACCESSOR):
                $data = [$docBlock, self::ACCESSOR, null];
                break;

            case [$docBlock, $prefix] = $this->getDocBlockFromMethod($class, $ucFirstProperty, self::MUTATOR):
                $data = [$docBlock, self::MUTATOR, $prefix];
                break;

            default:
                $data = [null, null, null];
        }

        return $this->docBlocks[$propertyHash] = $data;
    }

    private function getDocBlockFromProperty(string $class, string $property): ?DocBlock
    {
        // Use a ReflectionProperty instead of $class to get the parent class if applicable
        try {
            $reflectionProperty = new \ReflectionProperty($class, $property);
        } catch (\ReflectionException) {
            return null;
        }

        $reflector = $reflectionProperty->getDeclaringClass();

        foreach ($reflector->getTraits() as $trait) {
            if ($trait->hasProperty($property)) {
                return $this->getDocBlockFromProperty($trait->getName(), $property);
            }
        }

        try {
            return $this->docBlockFactory->create($reflectionProperty, $this->createFromReflector($reflector));
        } catch (\InvalidArgumentException|\RuntimeException) {
            return null;
        }
    }

    /**
     * @return array{DocBlock, string}|null
     */
    private function getDocBlockFromMethod(string $class, string $ucFirstProperty, int $type): ?array
    {
        $prefixes = self::ACCESSOR === $type ? $this->accessorPrefixes : $this->mutatorPrefixes;
        $prefix = null;

        foreach ($prefixes as $prefix) {
            $methodName = $prefix.$ucFirstProperty;

            try {
                $reflectionMethod = new \ReflectionMethod($class, $methodName);
                if ($reflectionMethod->isStatic()) {
                    continue;
                }

                if (
                    (self::ACCESSOR === $type && 0 === $reflectionMethod->getNumberOfRequiredParameters())
                    || (self::MUTATOR === $type && $reflectionMethod->getNumberOfParameters() >= 1)
                ) {
                    break;
                }
            } catch (\ReflectionException) {
                // Try the next prefix if the method doesn't exist
            }
        }

        if (!isset($reflectionMethod)) {
            return null;
        }

        $reflector = $reflectionMethod->getDeclaringClass();

        foreach ($reflector->getTraits() as $trait) {
            if ($trait->hasMethod($methodName)) {
                return $this->getDocBlockFromMethod($trait->getName(), $ucFirstProperty, $type);
            }
        }

        try {
            return [$this->docBlockFactory->create($reflectionMethod, $this->createFromReflector($reflector)), $prefix];
        } catch (\InvalidArgumentException|\RuntimeException) {
            return null;
        }
    }

    /**
     * Prevents a lot of redundant calls to ContextFactory::createForNamespace().
     */
    private function createFromReflector(\ReflectionClass $reflector): Context
    {
        $cacheKey = $reflector->getNamespaceName().':'.$reflector->getFileName();

        if (isset($this->contexts[$cacheKey])) {
            return $this->contexts[$cacheKey];
        }

        $this->contexts[$cacheKey] = $this->contextFactory->createFromReflector($reflector);

        return $this->contexts[$cacheKey];
    }
}
