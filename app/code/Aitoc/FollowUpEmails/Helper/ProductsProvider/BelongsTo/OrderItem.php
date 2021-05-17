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

use Aitoc\FollowUpEmails\Api\Helper\ProductsProviderInterface;
use Aitoc\FollowUpEmails\Helper\ProductsProvider\BelongsTo\ProductId as BelongsToProductIdProductsProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class OrderItem implements ProductsProviderInterface
{
    /**
     * @var BelongsToProductIdProductsProvider
     */
    private $belongsToProductIdProductsProvider;

    /**
     * OrderItem constructor.
     * @param ProductId $belongsToProductIdProductsProvider
     */
    public function __construct(BelongsToProductIdProductsProvider $belongsToProductIdProductsProvider)
    {
        $this->belongsToProductIdProductsProvider = $belongsToProductIdProductsProvider;
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

        return $this->getBelongsToProductIdProducts($productId, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }

    /**
     * @param int $productId
     * @param int $maxCount
     * @param array $excludeIds
     * @param $checkAvailabilityInStock
     * @return array
     */
    private function getBelongsToProductIdProducts($productId, $maxCount, $excludeIds, $checkAvailabilityInStock)
    {
        return $this->belongsToProductIdProductsProvider
            ->getProducts($productId, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }
}
