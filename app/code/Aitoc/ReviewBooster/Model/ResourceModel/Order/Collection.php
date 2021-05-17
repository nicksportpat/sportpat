<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Model\ResourceModel\Order;

use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

class Collection extends OrderCollection
{
    /**
     * joinSalesOrderTable
     */
    public function joinSalesOrderTable()
    {
        $this->getSelect()->join(
            ['items' => $this->getTable('sales_order_item')],
            'items.order_id = main_table.entity_id',
            'item_id'
        );
    }
}
