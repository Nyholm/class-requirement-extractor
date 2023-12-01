<?php

namespace Nyholm\ClassRequirementExtractor\Test\unit;

use Nyholm\ClassRequirementExtractor\Model\Requirement;
use Nyholm\ClassRequirementExtractor\Model\RequirementList;
use Nyholm\ClassRequirementExtractor\Model\RequirementMap;
use Nyholm\ClassRequirementExtractor\Test\Resources\Product;
use Nyholm\ClassRequirementExtractor\Test\Resources\ProductCategory;
use Nyholm\ClassRequirementExtractor\Test\Resources\ProductComment;

class CollectionRequirementTest extends BaseTestCase
{
    public function testList()
    {
        $req = self::$extractor->extract(Product::class);
        $this->assertInstanceOf(RequirementList::class, $req['comments']);

        /** @var RequirementList $commentsReq */
        $commentsReq = $req['comments'];
        // $this->assertEquals(15, $commentsReq->getMaxCount());
        $this->assertEquals(ProductComment::class, $commentsReq->getChildTypes()[0]);

        /** @var Requirement $child */
        $children = $commentsReq->getRequirements();
        $this->assertCount(2, $children);
        $this->assertArrayHasKey('author', $children);
        $this->assertArrayHasKey('text', $children);
    }

    public function testMap()
    {
        $req = self::$extractor->extract(Product::class);
        $this->assertInstanceOf(RequirementMap::class, $req['category']);

        /** @var RequirementMap $categoryReq */
        $categoryReq = $req['category'];
        $this->assertFalse($categoryReq->isNullable());
        $this->assertEquals(ProductCategory::class, $categoryReq->getChildTypes()[0]);

        /** @var Requirement $child */
        $children = $categoryReq->getRequirements();
        $this->assertCount(1, $children);
        $this->assertArrayHasKey('name', $children);
    }
}
