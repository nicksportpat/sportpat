<?php
/**
 * Copyright © 2017 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Scommerce\GoogleUniversalAnalytics\Model\Plugin;
class QuoteItemToOrderItem
{
	public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) 
	{
        /** @var $orderItem Item */
        $orderItem = $proceed($item, $additional);
		$orderItem->setGoogleCategory($item->getGoogleCategory());
        return $orderItem;
    }
}