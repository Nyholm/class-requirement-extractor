<?php

namespace Nyholm\ClassRequirementExtractor\Test\Resources;

use Symfony\Component\Validator\Constraints as Assert;

class ProductComment
{
    #[Assert\NotBlank()]
    public string $author = '';

    #[Assert\NotBlank()]
    #[Assert\Length(min: 10, max: 500)]
    public string $text = '';
}
