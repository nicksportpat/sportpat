<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Test\Unit\Model\Indexer;

use Amasty\Label\Model\Indexer\IndexBuilder;
use Amasty\Label\Model\Indexer\LabelIndexer;
use Amasty\Label\Test\Unit\Traits;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class LabelIndexerTest
 *
 * @see LabelIndexer
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class LabelIndexerTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers LabelIndexer::executeRow
     * @expectedException \Magento\Framework\Exception\LocalizedException
     */
    public function testExecuteRow()
    {
        $indexerRegistry = $this->createPartialMock(
            \Magento\Framework\Indexer\IndexerRegistry::class,
            ['get', 'isScheduled']
        );
        $indexBuilder = $this->createPartialMock(IndexBuilder::class, ['reindexByProductId']);

        $indexerRegistry->expects($this->any())->method('get')->willReturn($indexerRegistry);
        $indexerRegistry->expects($this->any())->method('get')->willReturnOnConsecutiveCalls(false, true);
        $indexBuilder->expects($this->once())->method('reindexByProductId');

        $model = $this->getObjectManager()->getObject(
            LabelIndexer::class,
            [
                'indexerRegistry' => $indexerRegistry,
                'indexBuilder' => $indexBuilder
            ]
        );

        $this->assertNull($model->executeRow(1));
        $model->executeRow(0);
        $this->assertNull($model->executeRow(1));
    }
}
