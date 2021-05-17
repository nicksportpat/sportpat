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
use Aitoc\FollowUpEmails\Helper\ProductsProvider\BelongsTo\Product as BelongsToProductProductsProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\Exception\NoSuchEntityException;

class ProductId implements ProductsProviderInterface
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var BelongsToProductProductsProvider
     */
    private $belongsToProductProductsProvider;

    /**
     * Product constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param Product $belongsToProductProductsProvider
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        BelongsToProductProductsProvider $belongsToProductProductsProvider
    ) {
        $this->productRepository = $productRepository;
        $this->belongsToProductProductsProvider = $belongsToProductProductsProvider;
    }

    /**
     * @param int $entityOrId
     * @param int $maxCount
     * @param array $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    public function getProducts($entityOrId, $maxCount = 0, $excludeIds = [], $checkAvailabilityInStock = false)
    {
        if (!$product = $this->getProductById($entityOrId)) {
            return [];
        };

        return $this->getBelongsToProductProducts($product, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }

    /**
     * @param $productId
     * @return ProductInterface|ProductModel
     */
    private function getProductById($productId)
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * @param ProductInterface $entity
     * @param int $maxCount
     * @param int[] $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return array
     */
    private function getBelongsToProductProducts($entity, $maxCount, $excludeIds, $checkAvailabilityInStock)
    {
        return $this->belongsToProductProductsProvider
            ->getProducts($entity, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }
}
