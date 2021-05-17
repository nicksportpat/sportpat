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
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\CatalogInventory\Api\StockRegistryInterface;

class Product implements ProductsProviderInterface
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @param ProductInterface $entityOrId
     * @param int $maxCount
     * @param array $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return array
     */
    public function getProducts($entityOrId, $maxCount = 0, $excludeIds = [], $checkAvailabilityInStock = false)
    {
        $products = [$entityOrId];

        if ($checkAvailabilityInStock && !$this->isAvailableInStock($entityOrId)) {
            return [];
        }

        return $this->applyRestrictions($products, $maxCount, $excludeIds);
    }

    /**
     * @param ProductInterface|ProductModel $productItem
     * @return bool
     */
    private function isAvailableInStock(ProductInterface $productItem)
    {
        $productItemId = $productItem->getId();
        $productItemWebsiteId = $productItem->getStore()->getWebsiteId();

        $stockItem = $this->getStockItem($productItemId, $productItemWebsiteId);

        return $stockItem->getIsInStock();
    }

    private function getStockItem($productId, $websiteId)
    {
        return $this->stockRegistry->getStockItem($productId, $websiteId);
    }

    /**
     * @param array $products
     * @param int $maxCount
     * @param int[] $excludeIds
     * @return array
     */
    private function applyRestrictions($products, $maxCount = 0, $excludeIds = [])
    {
        if ($excludeIds) {
            $products = $this->filterByExcluded($products, $excludeIds);
        }

        if ($maxCount) {
            $products = $this->limitByCount($products, $maxCount);
        }

        return $products;
    }

    /**
     * @param ProductInterface[] $products
     * @param int[] $excludeIds
     * @return array
     */
    private function filterByExcluded($products, $excludeIds)
    {
        $callback = function (ProductInterface $product) use ($excludeIds) {
            $productId = $product->getId();

            return !in_array($productId, $excludeIds);
        };

        return array_filter($products, $callback);
    }

    /**
     * @param ProductInterface[] $products
     * @param int $maxCount
     * @return ProductInterface[]
     */
    private function limitByCount($products, $maxCount)
    {
        return array_slice($products, 0, $maxCount);
    }
}