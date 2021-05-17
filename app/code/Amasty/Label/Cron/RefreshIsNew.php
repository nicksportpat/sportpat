<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Cron;

use Amasty\Label\Model\Indexer\LabelIndexer;
use Amasty\Label\Model\ResourceModel\Labels\CollectionFactory as LabelCollectionFactory;

/**
 * Class RefreshIsNew
 */
class RefreshIsNew
{
    /**
     * @var LabelCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var LabelIndexer
     */
    private $labelIndexer;

    public function __construct(LabelCollectionFactory $collectionFactory, LabelIndexer $labelIndexer)
    {
        $this->collectionFactory = $collectionFactory;
        $this->labelIndexer = $labelIndexer;
    }

    public function execute()
    {
        $collection = $this->collectionFactory->create()
            ->addActiveFilter()
            ->addIsNewFilterApplied();

        $ids = $collection->getAllIds();
        if ($ids) {
            $this->labelIndexer->executeByLabelIds($ids);
        }
    }
}
