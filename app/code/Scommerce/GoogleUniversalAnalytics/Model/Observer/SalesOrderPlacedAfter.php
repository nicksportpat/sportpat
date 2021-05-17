<?php
/**
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Scommerce\GoogleUniversalAnalytics\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class SalesOrderPlacedAfter implements ObserverInterface
{

    /**
     * @var \Scommerce\GoogleUniversalAnalytics\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper
     */
    public function __construct(
        \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
    }

    /**
     * Save google analytics cookie against order
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        if ($this->_helper->isEnhancedEcommerceEnabled()){
            $order = $observer->getEvent()->getOrder();
			if (isset($_COOKIE['_ga'])){
				$order->setGoogleCookie($_COOKIE['_ga'])
					->setGoogleTsCookie($this->_helper->_utmz)
					->save();
			}
        }
    }
}