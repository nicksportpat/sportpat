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

use Aitoc\FollowUpEmails\Helper\ProductsProvider\BaseWithNestedEntities;
use Aitoc\FollowUpEmails\Helper\ProductsProvider\BelongsTo\QuoteItem as BelongsToQuoteItemProductsProvider;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote as QuoteModel;

class Quote extends BaseWithNestedEntities
{
    /**
     * @var BelongsToQuoteItemProductsProvider
     */
    private $belongsToQuoteItemProductsProvider;

    /**
     * Quote constructor.
     * @param QuoteItem $belongsToQuoteItemProductsProvider
     */
    public function __construct(BelongsToQuoteItemProductsProvider $belongsToQuoteItemProductsProvider)
    {
        $this->belongsToQuoteItemProductsProvider = $belongsToQuoteItemProductsProvider;
    }

    /**
     * @param CartInterface|QuoteModel $entityOrId
     * @return QuoteItem[]
     */
    protected function getNestedEntities($entityOrId)
    {
        return $entityOrId->getAllItems();
    }

    /**
     * @inheritDoc
     */
    protected function getNestedEntityProducts($nestedEntity, $maxCount, $excludeIds, $checkAvailabilityInStock = false)
    {
        return $this->belongsToQuoteItemProductsProvider->getProducts($nestedEntity, $maxCount, $excludeIds, $checkAvailabilityInStock);
    }
}
