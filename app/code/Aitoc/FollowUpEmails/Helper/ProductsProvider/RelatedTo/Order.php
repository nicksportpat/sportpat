<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper\ProductsProvider\RelatedTo;

use Aitoc\FollowUpEmails\Helper\ProductsProvider\BaseWithNestedEntities;
use Aitoc\FollowUpEmails\Helper\ProductsProvider\RelatedTo\OrderItem as RelatedToOrderItemProductsProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class Order extends BaseWithNestedEntities
{
    /**
     * @var RelatedToOrderItemProductsProvider
     */
    private $relatedToOrderItemProductsProvider;

    /**
     * Order constructor.
     * @param RelatedToOrderItemProductsProvider $relatedToOrderItemProductsProvider
     */
    public function __construct(RelatedToOrderItemProductsProvider $relatedToOrderItemProductsProvider)
    {
        $this->relatedToOrderItemProductsProvider = $relatedToOrderItemProductsProvider;
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
        return $this->getOrderItemRelatedProducts($nestedEntity, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }

    /**
     * @param OrderItemInterface $orderItem
     * @param int $maxCount
     * @param int[] $excludeIds
     * @param $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    private function getOrderItemRelatedProducts($orderItem, $maxCount, $excludeIds, $checkAvailabilityInStock)
    {
        return $this->relatedToOrderItemProductsProvider->getProducts($orderItem, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }

    /**
     * @inheritDoc
     */
    protected function getBelongsToEntityOrIdProducts($entityOrId)
    {
        // TODO: Implement getBelongsToEntityOrIdProducts() method.
    }
}
