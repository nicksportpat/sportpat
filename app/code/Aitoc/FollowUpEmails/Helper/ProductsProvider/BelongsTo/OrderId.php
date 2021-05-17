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
use Aitoc\FollowUpEmails\Helper\ProductsProvider\BelongsTo\Order as BelongsToOrderProductsProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderId implements ProductsProviderInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var BelongsToOrderProductsProvider
     */
    private $belongsToOrderProductsProvider;

    /**
     * OrderId constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param Order $belongsToOrderProductsProvider
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        BelongsToOrderProductsProvider $belongsToOrderProductsProvider)
    {
        $this->orderRepository = $orderRepository;
        $this->belongsToOrderProductsProvider = $belongsToOrderProductsProvider;
    }

    /**
     * @param $entityOrId
     * @param int $maxCount
     * @param array $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    public function getProducts($entityOrId, $maxCount = 0, $excludeIds = [], $checkAvailabilityInStock = false)
    {
        if (!$order = $this->getOrderById($entityOrId)) {
            return [];
        }

        return $this->getProductsByOrder($order, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }

    /**
     * @param int $orderId
     * @return OrderInterface
     */
    private function getOrderById($orderId)
    {
        return $this->orderRepository->get($orderId);
    }

    /**
     * @param OrderInterface $order
     * @param int $maxCount
     * @param int[] $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    private function getProductsByOrder($order, $maxCount, $excludeIds, $checkAvailabilityInStock)
    {
        return $this->belongsToOrderProductsProvider->getProducts($order, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }
}
