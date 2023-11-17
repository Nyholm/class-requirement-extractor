<?php

namespace Nyholm\ClassRequirementExtractor;

class Requirement
{
    private string $name;
    private bool $writeable;
    private bool $readable;
    private ?string $type = null;
    private ?bool $required = null;
    private ?bool $allowEmptyValue = null;
    private ?string $example = null;
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

    /**
     * @return bool
     */
    public function isWriteable(): bool
    {
        return $this->writeable;
    }

    /**
     * @return bool
     */
    public function isReadable(): bool
    {
        return $this->readable;
    }


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(?bool $required): void
    {
        $this->required = $required;
    }

    public function getAllowEmptyValue(): ?bool
    {
        return $this->allowEmptyValue;
    }

    public function setAllowEmptyValue(?bool $allowEmptyValue): void
    {
        $this->allowEmptyValue = $allowEmptyValue;
    }

    public function getExample(): ?string
    {
        return $this->example;
    }

    public function setExample(?string $example): void
    {
        $this->example = $example;
    }

    public function getChildRequirements(): array
    {
        return $this->childRequirements;
    }

    public function setChildRequirements(array $childRequirements): void
    {
        $this->childRequirements = $childRequirements;
    }
}
