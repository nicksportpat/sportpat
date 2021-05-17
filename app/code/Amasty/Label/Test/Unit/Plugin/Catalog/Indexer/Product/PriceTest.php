<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Test\Unit\Plugin\Catalog\Indexer\Product;

use Amasty\Label\Model\Indexer\LabelIndexer;
use Amasty\Label\Plugin\Catalog\Indexer\Product\Price;
use Amasty\Label\Test\Unit\Traits;
use Magento\Catalog\Cron\RefreshSpecialPrices;
use Magento\Catalog\Model\Indexer\Product\Price as PriceIndexer;

/**
 * Class PriceTest
 *
 * @see Price
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class PriceTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers Price::afterExecuteList
     */
    public function testAfterExecuteList()
    {
        $plugin = $this->createPartialMock(Price::class, []);
        $priceIndexer = $this->createPartialMock(PriceIndexer::class, []);
        $labelIndexer = $this->createPartialMock(LabelIndexer::class, ['execute']);
        $this->setProperty($plugin, 'labelIndexer', $labelIndexer, Price::class);
        $labelIndexer->expects($this->once())->method('execute');
        $this->setProperty($plugin, 'needReindex', true, Price::class);
        $plugin->afterExecuteList($priceIndexer);
        $this->setProperty($plugin, 'needReindex', false, Price::class);
        $plugin->afterExecuteList($priceIndexer);
    }

    /**
     * @covers Price::beforeExecute
     */
    public function testBeforeExecute()
    {
        $plugin = $this->createPartialMock(Price::class, []);
        $subject = $this->createPartialMock(RefreshSpecialPrices::class, []);
        $plugin->beforeExecute([], []);
        $this->assertFalse($this->getProperty($plugin, 'needReindex', Price::class));
        $plugin->beforeExecute($subject, []);
        $this->assertTrue($this->getProperty($plugin, 'needReindex', Price::class));
    }
}
