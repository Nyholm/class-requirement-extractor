<?php

namespace Nyholm\ClassRequirementExtractor\Test\unit;

use Nyholm\ClassRequirementExtractor\ExtractorFactory;
use Nyholm\ClassRequirementExtractor\RequirementExtractor;
use Nyholm\ClassRequirementExtractor\Test\Resources\CreateCompany;
use Nyholm\ClassRequirementExtractor\Test\Resources\Simple;
use PHPUnit\Framework\TestCase;

class RequirementExtractorTest extends TestCase
{
    private static RequirementExtractor $extractor;

    public static function setUpBeforeClass(): void
    {
        self::$extractor = ExtractorFactory::create();
        parent::setUpBeforeClass();
    }

    public function testSimple()
    {
        $req = self::$extractor->extract(Simple::class);
        $this->assertCount(6, $req);

        $this->assertEquals('firstName', $req['firstName']->getName());
        $this->assertFalse($req['firstName']->isReadable());
        $this->assertFalse($req['firstName']->isWriteable());
        $this->assertEquals('string', $req['firstName']->getTypes()[0]);
        $this->assertFalse($req['firstName']->isNullable());
        $this->assertFalse($req['firstName']->getAllowEmptyValue());

        $this->assertEquals('lastName', $req['lastName']->getName());
        $this->assertFalse($req['lastName']->isReadable());
        $this->assertFalse($req['lastName']->isWriteable());
        $this->assertEquals('string', $req['lastName']->getTypes()[0]);
        $this->assertTrue($req['lastName']->isNullable());
        $this->assertTrue($req['lastName']->getAllowEmptyValue());

        $this->assertEquals('age', $req['age']->getName());
        $this->assertTrue($req['age']->isReadable());
        $this->assertFalse($req['age']->isWriteable());
        $this->assertEquals('int', $req['age']->getTypes()[0]);

        $this->assertEquals('hobby', $req['hobby']->getName());
        $this->assertFalse($req['hobby']->isReadable());
        $this->assertFalse($req['hobby']->isWriteable());
        $this->assertFalse($req['hobby']->hasType());

        $this->assertEquals('color', $req['color']->getName());
        $this->assertFalse($req['color']->isReadable());
        $this->assertTrue($req['color']->isWriteable());
        $this->assertEquals('string', $req['color']->getTypes()[0]);
        $this->assertTrue($req['color']->getAllowEmptyValue());

        $this->assertEquals('paid', $req['paid']->getName());
        $this->assertFalse($req['paid']->isReadable());
        $this->assertTrue($req['paid']->isWriteable());
        $this->assertEquals('bool', $req['paid']->getTypes()[0]);
    }

    public function testExample()
    {
        $req = self::$extractor->extract(CreateCompany::class);
        $this->assertEquals(['Volvo AB'], $req['name']->getExamples());
        $this->assertTrue($req['name']->isReadable());
        $this->assertTrue($req['name']->isWriteable());
        $this->assertEquals('string', $req['name']->getTypes()[0]);
    }
}
