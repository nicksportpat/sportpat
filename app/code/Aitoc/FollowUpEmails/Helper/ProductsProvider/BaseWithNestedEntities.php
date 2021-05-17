<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper\ProductsProvider;

use Magento\Catalog\Api\Data\ProductInterface;
use Aitoc\FollowUpEmails\Api\Helper\ProductsProviderInterface;

abstract class BaseWithNestedEntities implements ProductsProviderInterface
{
    /**
     * @param $entityOrId
     * @return array
     */
    abstract protected function getNestedEntities($entityOrId);

    /**
     * @param mixed $nestedEntity
     * @param int $maxCount
     * @param int[] $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    abstract protected function getNestedEntityProducts($nestedEntity, $maxCount, $excludeIds, $checkAvailabilityInStock = false);

    /**
     * @param $entityOrId
     * @param int $maxCount
     * @param array $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    public function getProducts($entityOrId, $maxCount = 0, $excludeIds = [], $checkAvailabilityInStock = false)
    {
        $isLimited = (bool) $maxCount;

        $nestedEntities = $this->getNestedEntities($entityOrId);

        if (!$nestedEntities) {
            return [];
        }

        $entityRelatedProducts = [];

        foreach ($nestedEntities as $nestedEntity) {
            $nestedEntityRelatedProducts = $this->getNestedEntityProducts($nestedEntity, $maxCount, $excludeIds, $checkAvailabilityInStock);
            $entityRelatedProducts = array_merge($entityRelatedProducts, $nestedEntityRelatedProducts);

            if ($isLimited) {
                $maxCount -= count($nestedEntityRelatedProducts);

                if ($maxCount <= 0) {
                    break;
                }
            }

            $excludeIds = $this->adjustExcludeIds($excludeIds, $nestedEntityRelatedProducts);
        }

        return $entityRelatedProducts;
    }

    /**
     * @param int[] $excludeIds
     * @param ProductInterface[] $newProducts
     * @return int[]
     */
    private function adjustExcludeIds($excludeIds, $newProducts)
    {
        foreach ($newProducts as $newProduct) {
            $excludeIds[] = $newProduct->getId();
        }

        return $excludeIds;
    }
}
