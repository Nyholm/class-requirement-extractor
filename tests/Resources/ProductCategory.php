<?php

namespace Nyholm\ClassRequirementExtractor\Test\Resources;

use Symfony\Component\Validator\Constraints as Assert;

class ProductCategory
{
    #[Assert\NotBlank()]
    public string $name = '';
}
