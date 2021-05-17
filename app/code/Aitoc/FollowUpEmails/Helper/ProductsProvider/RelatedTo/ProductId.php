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

use Aitoc\FollowUpEmails\Api\Helper\ProductsProviderInterface;
use Aitoc\FollowUpEmails\Helper\ProductsProvider\RelatedTo\Product as ToProductRelatedProductsProvider;
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
     * @var ToProductRelatedProductsProvider
     */
    private $toProductRelatedProductsProvider;

    /**
     * Product constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param ToProductRelatedProductsProvider $toProductRelatedProductsProvider
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ToProductRelatedProductsProvider $toProductRelatedProductsProvider
    ) {
        $this->productRepository = $productRepository;
        $this->toProductRelatedProductsProvider = $toProductRelatedProductsProvider;
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

        return $this->getRelatedProductsByEntity($product, $maxCount, $excludeIds, $checkAvailabilityInStock);
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
    private function getRelatedProductsByEntity($entity, $maxCount, $excludeIds, $checkAvailabilityInStock)
    {
        return $this->toProductRelatedProductsProvider->getProducts($entity, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }
}
