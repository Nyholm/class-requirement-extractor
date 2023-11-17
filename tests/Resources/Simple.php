<?php

namespace Nyholm\ClassRequirementExtractor\Test\Resources;

use Symfony\Component\Validator\Constraints as Assert;

class Simple
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $firstName;
    private ?string $lastName;
    private int $age;
    private $hobby;
    private $color;
    private bool $paid;

    public function getAge(): int
    {
        return $this->age;
    }

    public function setPaid(bool $paid): void
    {
        $this->paid = $paid;
    }

    /**
     * @param string $color
     */
    public function setColor($color): void
    {
        $this->color = $color;
    }
}
