<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Model;

use Aitoc\ReviewBooster\Model\ResourceModel\Order\Collection as OrderCollection;
use Aitoc\ReviewBooster\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * Class OrderManagement
 */
class OrderManagement
{
    const FIELD_NAME_PRODUCT_ID = 'product_id';
    const FIELD_NAME_CUSTOMER_ID = 'customer_id';

    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * OrderManagement constructor.
     * @param OrderCollectionFactory $orderCollectionFactory
     */
    public function __construct(OrderCollectionFactory $orderCollectionFactory)
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Has customer purchased product
     *
     * @param int $customerId
     * @param int $productId
     * @return bool
     */
    public function isCustomerPurchasedProduct($customerId, $productId)
    {
        $salesOrdersCollection = $this->createOrderCollection();
        $salesOrdersCollection->joinSalesOrderTable();
        $salesOrdersCollection
            ->addFieldToFilter(self::FIELD_NAME_PRODUCT_ID, $productId)
            ->addFieldToFilter(self::FIELD_NAME_CUSTOMER_ID, $customerId)
        ;

        return (bool) $salesOrdersCollection->getSize();
    }

    /**
     * @return OrderCollection
     */
    private function createOrderCollection()
    {
        return $this->orderCollectionFactory->create();
    }
}
