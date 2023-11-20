<?php

namespace Nyholm\ClassRequirementExtractor\Test\Resources;

use Symfony\Component\Validator\Constraints as Assert;

class ParentClass
{
    /**
     * @deprecated Dont use this
     */
    #[Assert\NotBlank()]
    private string $deprecated;

    public function getDeprecated(): string
    {
        return $this->deprecated;
    }

    public function setDeprecated(string $deprecated): void
    {
        $this->deprecated = $deprecated;
    }
}
