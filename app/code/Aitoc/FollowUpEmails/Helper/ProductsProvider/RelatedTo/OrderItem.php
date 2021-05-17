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

use Aitoc\FollowUpEmails\Helper\ProductsProvider\RelatedTo\ProductId as ToProductIdRelatedProductsProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Aitoc\FollowUpEmails\Api\Helper\ProductsProviderInterface;

class OrderItem implements ProductsProviderInterface
{
    /**
     * @var ToProductIdRelatedProductsProvider
     */
    private $toProductIdRelatedProductsProvider;

    public function __construct(ToProductIdRelatedProductsProvider $toProductIdRelatedProductsProvider)
    {
        $this->toProductIdRelatedProductsProvider = $toProductIdRelatedProductsProvider;
    }

    /**
     * @param OrderItemInterface $entityOrId
     * @param int $maxCount
     * @param array $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    public function getProducts($entityOrId, $maxCount = 0, $excludeIds = [], $checkAvailabilityInStock = false)
    {
        $productId = $entityOrId->getProductId();

        return $this->getRelatedProductsByProductId($productId, $maxCount, $excludeIds);
    }

    /**
     * @param int $productId
     * @param int $maxCount
     * @param array $excludeIds
     * @return array
     */
    private function getRelatedProductsByProductId($productId, $maxCount, $excludeIds)
    {
        return $this->toProductIdRelatedProductsProvider->getProducts($productId, $maxCount, $excludeIds);
    }
}
