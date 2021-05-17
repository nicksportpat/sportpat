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
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item;

class QuoteItem implements ProductsProviderInterface
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * QuoteItem constructor.
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(StockRegistryInterface $stockRegistry)
    {
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @param CartItemInterface|Item $entityOrId
     * @param int $maxCount
     * @param array $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    public function getProducts($entityOrId, $maxCount = 0, $excludeIds = [], $checkAvailabilityInStock = false)
    {
        if ($entityOrId->getParentItemId()) {
            return [];
        }

        if (!$product = $entityOrId->getProduct()) {
            return [];
        }

        if ($checkAvailabilityInStock && !$this->isAvailableInStock($product)) {
            return [];
        }

        if ($excludeIds && in_array($product, $excludeIds)) {
            return [];
        }

        return [$product];
    }

    /**
     * @param Product|ProductInterface $product
     * @return bool
     */
    private function isAvailableInStock(ProductInterface $product)
    {
        $productItemId = $product->getId();
        $productItemWebsiteId = $product->getStore()->getWebsiteId();

        $stockItem = $this->getStockItem($productItemId, $productItemWebsiteId);

        return $stockItem->getIsInStock();
    }

    /**
     * @param int $productId
     * @param int $websiteId
     * @return StockItemInterface
     */
    private function getStockItem($productId, $websiteId)
    {
        return $this->stockRegistry->getStockItem($productId, $websiteId);
    }
}
