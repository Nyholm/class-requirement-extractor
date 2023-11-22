<?php

namespace Nyholm\ClassRequirementExtractor\Test\Resources;

use Symfony\Component\Validator\Constraints as Assert;

class Product
{
    #[Assert\NotBlank()]
    public string $name = '';

    #[Assert\NotBlank()]
    public ?ProductCategory $category = null;

    /**
     * @var ProductComment[]
     */
    #[Assert\All([
        new Assert\Count(max: 15),
        new Assert\Type(ProductComment::class),
    ])]
    public $comments;
}
