<?php

namespace Nyholm\ClassRequirementExtractor;

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
    private array $childRequirements = [];

    public function __construct(string $name, bool $writeable, bool $readable)
    {
        $this->name = $name;
        $this->writeable = $writeable;
        $this->readable = $readable;
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
        return $this->types;
    }

    public function addType(?string $type): void
    {
        $this->types[] = $type;
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

    public function getChildRequirements(): array
    {
        return $this->childRequirements;
    }

    public function setChildRequirements(array $childRequirements): void
    {
        $this->childRequirements = $childRequirements;
    }

    public function isDeprecated(): ?bool
    {
        return $this->deprecated;
    }

    public function setDeprecated(?bool $deprecated): void
    {
        $this->deprecated = $deprecated;
    }
}
