<?php

namespace Nyholm\ClassRequirementExtractor\Test\unit;

use Nyholm\ClassRequirementExtractor\ExtractorFactory;
use Nyholm\ClassRequirementExtractor\RequirementExtractor;
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

        $this->assertEquals('lastName', $req['lastName']->getName());
        $this->assertFalse($req['lastName']->isReadable());
        $this->assertFalse($req['lastName']->isWriteable());

        $this->assertEquals('age', $req['age']->getName());
        $this->assertTrue($req['age']->isReadable());
        $this->assertFalse($req['age']->isWriteable());

        $this->assertEquals('hobby', $req['hobby']->getName());
        $this->assertFalse($req['hobby']->isReadable());
        $this->assertFalse($req['hobby']->isWriteable());

        $this->assertEquals('color', $req['color']->getName());
        $this->assertFalse($req['color']->isReadable());
        $this->assertTrue($req['color']->isWriteable());

        $this->assertEquals('paid', $req['paid']->getName());
        $this->assertFalse($req['paid']->isReadable());
        $this->assertTrue($req['paid']->isWriteable());



        $x = 2;
    }
}