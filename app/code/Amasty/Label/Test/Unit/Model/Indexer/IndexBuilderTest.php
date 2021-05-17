<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Test\Unit\Model\Indexer;

use Amasty\Label\Model\Indexer\IndexBuilder;
use Amasty\Label\Model\Labels;
use Amasty\Label\Model\ResourceModel\Labels\CollectionFactory;
use Amasty\Label\Test\Unit\Traits;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class IndexBuilderTest
 *
 * @see IndexBuilder
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class IndexBuilderTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @var IndexBuilder|MockObject
     */
    private $model;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    protected function setUp()
    {
        $this->collectionFactory = $this->createPartialMock(
            CollectionFactory::class,
            ['create', 'addActiveFilter', 'addFieldToFilter']
        );
        $this->model = $this->getObjectManager()->getObject(
            IndexBuilder::class,
            [
                'collectionFactory' => $this->collectionFactory
            ]
        );
    }

    /**
     * @covers IndexBuilder::getFullLabelCollection
     */
    public function testGetFullLabelCollection()
    {
        $this->collectionFactory->expects($this->once())->method('create')->willReturn($this->collectionFactory);
        $this->invokeMethod($this->model, 'getFullLabelCollection');
        $this->setProperty($this->model, 'fullLabelCollection', 'test', IndexBuilder::class);
        $this->assertEquals('test', $this->invokeMethod($this->model, 'getFullLabelCollection'));
    }

    /**
     * @covers IndexBuilder::getLabelCollection
     */
    public function testGetLabelCollection()
    {
        $this->collectionFactory->expects($this->any())->method('create')->willReturn($this->collectionFactory);
        $this->collectionFactory->expects($this->any())->method('addActiveFilter')->willReturn($this->collectionFactory);
        $this->collectionFactory->expects($this->once())->method('addFieldToFilter');
        $this->invokeMethod($this->model, 'getLabelCollection');
        $this->invokeMethod($this->model, 'getLabelCollection', [1]);
    }

    /**
     * @covers IndexBuilder::prepareData
     */
    public function testPrepareData()
    {
        $result = [
            [[
                'product_id' => 1,
                'label_id' => null,
                'store_id' => 1,
            ]],
            [1]
        ];
        $label = $this->createPartialMock(
            Labels::class,
            ['getLabelMatchingProductIds', 'getStoreIds', 'getId']
        );

        $label->expects($this->any())->method('getLabelMatchingProductIds')
            ->willReturnOnConsecutiveCalls([], [1 => [1, 2]]);
        $label->expects($this->any())->method('getStoreIds')->willReturnOnConsecutiveCalls([], [1, 2]);

        $this->assertEquals([[], []], $this->invokeMethod($this->model, 'prepareData', [$label, [1, 2]]));
        $this->assertEquals($result, $this->invokeMethod($this->model, 'prepareData', [$label, [1, 2]]));
    }
}
