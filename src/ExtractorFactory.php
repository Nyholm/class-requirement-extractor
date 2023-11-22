<?php

namespace Nyholm\ClassRequirementExtractor;

use Nyholm\ClassRequirementExtractor\AttributeProcessor\NotBlankProcessor;
use Nyholm\ClassRequirementExtractor\AttributeProcessor\NotNullProcessor;
use Nyholm\ClassRequirementExtractor\AttributeProcessor\TypeProcessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;

/**
 * This is a good class to use if you don't have proper dependency injection.
 */
class ExtractorFactory
{
    public static function create(): RequirementExtractor
    {
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $phpstanExtractor = new PhpStanExtractor();

        $typesExtractor = new AllTypeExtractor([$phpstanExtractor, $phpDocExtractor, $reflectionExtractor]);

        return new RequirementExtractor($reflectionExtractor, $typesExtractor, new DocBlockParser(), [
            new NotBlankProcessor(),
            new TypeProcessor(),
            new NotNullProcessor(),
        ]);
    }
}
