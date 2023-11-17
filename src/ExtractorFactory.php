<?php

namespace Nyholm\ClassRequirementExtractor;

use Nyholm\ClassRequirementExtractor\AttributeProcessor\NotBlankProcessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

/**
 * This is a good class to use if you don't have proper dependency injection.
 */
class ExtractorFactory
{
    public static function create()
    {
        return new RequirementExtractor(self::getPropertyExtractor(), [
            new NotBlankProcessor(),
        ]);
    }

    private static function getPropertyExtractor(): PropertyInfoExtractorInterface
    {
        $phpDocExtractor = new PhpDocExtractor();
        $reflectionExtractor = new ReflectionExtractor();
        $phpstanExtractor = new PhpStanExtractor();

        $listExtractors = [$reflectionExtractor];
        $typeExtractors = [$phpstanExtractor, $phpDocExtractor, $reflectionExtractor];
        $descriptionExtractors = [$phpDocExtractor];
        $accessExtractors = [$reflectionExtractor];
        $propertyInitializableExtractors = [$reflectionExtractor];

        return new PropertyInfoExtractor(
            $listExtractors,
            $typeExtractors,
            $descriptionExtractors,
            $accessExtractors,
            $propertyInitializableExtractors
        );
    }
}
