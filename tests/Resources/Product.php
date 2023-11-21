<?php

namespace Nyholm\ClassRequirementExtractor\Test\Resources;

use Symfony\Component\Validator\Constraints as Assert;

class Product
{
    #[Assert\NotBlank()]
    public string $name = '';

    #[Assert\NotBlank()]
    #[Assert\Valid()]
    public ?ProductCategory $category = null;

    #[Assert\All([
        new Assert\Valid(),
        new Assert\Count(max: 15),
        new Assert\Type(ProductComment::class)
    ])]
    public array $comments = [];
}
