<?php

namespace Nyholm\ClassRequirementExtractor\Test\unit;

use Nyholm\ClassRequirementExtractor\ExtractorFactory;
use Nyholm\ClassRequirementExtractor\RequirementExtractor;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected static RequirementExtractor $extractor;

    public static function setUpBeforeClass(): void
    {
        self::$extractor = ExtractorFactory::create();
        parent::setUpBeforeClass();
    }
}
