<?php

namespace Nyholm\ClassRequirementExtractor\Model;

class Requirement
{
    private string $name;
    private bool $writeable;
    private bool $readable;
    private ?bool $deprecated = null;
    private array $types = [];
    private ?bool $nullable = null;
    private bool $allowEmptyValue = true;
    private array $examples = [];

    /**
     * @var \ReflectionAttribute[]
     */
    private array $attributes = [];

    public function __construct(string $name, bool $writeable, bool $readable, array $attributes = [])
    {
        $this->name = $name;
        $this->writeable = $writeable;
        $this->readable = $readable;
        $this->attributes = $attributes;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isWriteable(): bool
    {
        return $this->writeable;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    public function hasType()
    {
        return [] !== $this->getTypes();
    }

    public function getTypes(): array
    {
        return array_keys($this->types);
    }

    public function addType(string $type): void
    {
        if ('boolean' === $type) {
            $type = 'bool';
        } elseif ('integer' === $type) {
            $type = 'int';
        }

        $this->types[$type] = true;
    }

    public function isNullable(): ?bool
    {
        return $this->nullable;
    }

    public function setNullable(?bool $nullable): void
    {
        $this->nullable = $nullable;
    }

    public function getAllowEmptyValue(): ?bool
    {
        return $this->allowEmptyValue;
    }

    public function setAllowEmptyValue(bool $allowEmptyValue): void
    {
        $this->allowEmptyValue = $allowEmptyValue;
    }

    public function hasExamples(): bool
    {
        return [] !== $this->examples;
    }

    public function getExamples(): array
    {
        return $this->examples;
    }

    public function addExample(string $example): void
    {
        $this->examples[] = $example;
    }

    public function isDeprecated(): ?bool
    {
        return $this->deprecated;
    }

    public function setDeprecated(?bool $deprecated): void
    {
        $this->deprecated = $deprecated;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param class-string $class
     */
    public function hasAttributes(string $class): bool
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() === $class) {
                return true;
            }
        }
        return false;
    }
}
