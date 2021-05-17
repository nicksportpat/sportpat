<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\Observer\Backend;

use Magento\Framework\Event\Observer;
use Magento\Framework\Indexer\IndexerRegistry;
use Amasty\Label\Model\Indexer\LabelIndexer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CatalogProductSaveAfterObserver
 * @package Amasty\Label\Model\Observer\Backend
 */
class CatalogProductSaveAfterObserver implements ObserverInterface
{
    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    public function __construct(IndexerRegistry $indexerRegistry)
    {
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        if ($product) {
            $this->indexerRegistry->get(LabelIndexer::INDEXER_ID)->reindexRow($product->getId());
        }
    }
}
