<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper\ProductsProvider\BelongsTo;

use Aitoc\FollowUpEmails\Helper\ProductsProvider\BaseWithNestedEntities;
use Aitoc\FollowUpEmails\Helper\ProductsProvider\BelongsTo\OrderItem as BelongsToOrderItemProductsProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class Order extends BaseWithNestedEntities
{
    /**
     * @var BelongsToOrderItemProductsProvider
     */
    private $belongsToOrderItemProductsProvider;

    /**
     * Order constructor.
     * @param BelongsToOrderItemProductsProvider $belongsToOrderItemProductsProvider
     */
    public function __construct(BelongsToOrderItemProductsProvider $belongsToOrderItemProductsProvider)
    {
        $this->belongsToOrderItemProductsProvider = $belongsToOrderItemProductsProvider;
    }

    /**
     * @param OrderInterface $entityOrId
     * @return OrderItemInterface[]
     */
    protected function getNestedEntities($entityOrId)
    {
        return $entityOrId->getItems();
    }

    /**
     * @param OrderItemInterface $nestedEntity
     * @param int $maxCount
     * @param int[] $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    protected function getNestedEntityProducts($nestedEntity, $maxCount, $excludeIds, $checkAvailabilityInStock = false)
    {
        return $this->getBelongsToOrderItemProducts($nestedEntity, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }

    /**
     * @param OrderItemInterface $orderItem
     * @param int $maxCount
     * @param int[] $excludeIds
     * @return ProductInterface[]
     */
    private function getBelongsToOrderItemProducts($orderItem, $maxCount, $excludeIds)
    {
        return $this->belongsToOrderItemProductsProvider->getProducts($orderItem, $maxCount, $excludeIds);
    }
}
