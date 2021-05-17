<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\App\Helper\Context;

/**
 * Class Stock
 */
class Stock extends AbstractHelper
{
    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Status
     */
    private $stockResource;

    public function __construct(
        Context $context,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockResource
    ) {
        parent::__construct($context);
        $this->stockResource = $stockResource;
    }

    /**
     * @param ProductCollection $productCollection
     * @param int|null $stockValue
     */
    public function addStockFilter(ProductCollection $productCollection, $stockValue = null)
    {
        $this->stockResource->addStockDataToCollection($productCollection, false);

        if ($stockValue !== null) {
            $productCollection->getSelect()->where(
                sprintf('stock_status_index.%s=%s', $this->getSaleableColumn($productCollection), $stockValue)
            );
        }

        $productCollection->getSelect()->columns($this->getQtyColumn($productCollection), 'stock_status_index');
    }

    /**
     * @param ProductCollection $productCollection
     *
     * @return string
     */
    protected function getSaleableColumn(ProductCollection $productCollection)
    {
        if ($this->isMsiEnabled($productCollection)) {
            $salableColumn = 'is_salable';
        } else {
            $salableColumn = 'stock_status';
        }

        return $salableColumn;
    }
    /**
     * @param ProductCollection $productCollection
     *
     * @return string
     */
    protected function getQtyColumn(ProductCollection $productCollection)
    {
        if ($this->isMsiEnabled($productCollection)) {
            $salableColumn = 'quantity';
        } else {
            $salableColumn = 'qty';
        }

        return $salableColumn;
    }

    /**
     * @param ProductCollection $productCollection
     *
     * @return bool
     */
    protected function isMsiEnabled(ProductCollection $productCollection)
    {
        $fromTables = $productCollection->getSelect()->getPart('from');
        return $this->_moduleManager->isEnabled('Magento_Inventory')
            && $fromTables['stock_status_index']['tableName'] !=
            $productCollection->getResource()->getTable('cataloginventory_stock_status');
    }
}
