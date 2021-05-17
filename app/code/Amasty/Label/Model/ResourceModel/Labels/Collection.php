<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\ResourceModel\Labels;

/**
 * Class Collection
 * @package Amasty\Label\Model\ResourceModel\Labels
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\Label\Model\Labels::class, \Amasty\Label\Model\ResourceModel\Labels::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * @return $this
     */
    public function addActiveFilter()
    {
        $this->addFieldToFilter('status', 1);

        return $this;
    }

    /**
     * @return $this
     */
    public function addIsNewFilterApplied()
    {
        $this->addFieldToFilter('is_new', ['in' => [1, 2]]);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getItemById($idValue)
    {
        return $this->_items[$idValue] ?? null;
    }
}
