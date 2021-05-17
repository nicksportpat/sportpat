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
use Aitoc\FollowUpEmails\Helper\ProductsProvider\BelongsTo\Quote as BelongsToQuoteProductsProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Quote\Api\Data\CartInterface;

class QuoteId implements ProductsProviderInterface
{
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var BelongsToQuoteProductsProvider
     */
    private $belongsToQuoteProductsProvider;

    /**
     * QuoteId constructor.
     * @param CartRepositoryInterface $cartRepository
     * @param Quote $belongsToQuoteProductsProvider
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        BelongsToQuoteProductsProvider $belongsToQuoteProductsProvider
    ) {
        $this->cartRepository = $cartRepository;
        $this->belongsToQuoteProductsProvider = $belongsToQuoteProductsProvider;
    }

    /**
     * @param int $entityOrId
     * @param int $maxCount
     * @param array $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     * @throws NoSuchEntityException
     */
    public function getProducts($entityOrId, $maxCount = 0, $excludeIds = [], $checkAvailabilityInStock = false)
    {
        if (!$quote = $this->getQuoteById($entityOrId)) {
            return [];
        }

        return $this->getProductsByQuote($quote, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }

    /**
     * @param $quoteId
     * @return CartInterface
     * @throws NoSuchEntityException
     */
    private function getQuoteById($quoteId)
    {
        return $this->cartRepository->get($quoteId);
    }

    /**
     * @param CartInterface $quote
     * @param int $maxCount
     * @param int[] $excludeIds
     * @param bool $checkAvailabilityInStock
     * @return ProductInterface[]
     */
    private function getProductsByQuote($quote, $maxCount, $excludeIds, $checkAvailabilityInStock)
    {
        return $this->belongsToQuoteProductsProvider
            ->getProducts($quote, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }
}
