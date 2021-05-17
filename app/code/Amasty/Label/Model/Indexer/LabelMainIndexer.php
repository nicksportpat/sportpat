<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\Indexer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class LabelMainIndexer
 * @package Amasty\Label\Model\Indexer
 */
class LabelMainIndexer extends LabelIndexer
{
    /**
     * Execute materialization on ids entities
     * @param int[] $ids
     */
    public function execute($ids)
    {
        $this->executeByLabelIds($ids);
    }
}
