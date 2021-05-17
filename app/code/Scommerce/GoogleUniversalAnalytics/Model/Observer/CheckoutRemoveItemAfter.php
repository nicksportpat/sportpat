<?php
/**
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Scommerce\GoogleUniversalAnalytics\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class CheckoutRemoveItemAfter implements ObserverInterface
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Scommerce\GoogleUniversalAnalytics\Helper\Data
     */
    protected $_helper;

	/**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
	protected $_coreSession;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     * @param \Magento\Framework\Session\SessionManagerInterface $coresession,
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager,
								\Magento\Framework\Session\SessionManagerInterface $coresession,
                                \Magento\Framework\App\Request\Http $request,
                                \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper)
    {
        $this->_objectManager = $objectmanager;
		$this->_coreSession = $coresession;
        $this->_request = $request;
        $this->_helper = $helper;
    }

    public function execute(EventObserver $observer)
    {
        if ($this->_helper->isEnhancedEcommerceEnabled()){

			$quoteItem = $observer->getQuoteItem();
            $product = $quoteItem->getProduct();
			$category = $quoteItem->getGoogleCategory();
			
			if (!isset($category)){
				$category = $this->_helper->getProductCategoryName($product);
			}

            $productOutBasket = array(
                    'id' => $product->getSku(),
                    'name' => $product->getName(),
                    'category' => $category,
                    'brand' => $this->_helper->getBrand($product),
                    'price' => $product->getFinalPrice(),
                    'qty'=> $quoteItem->getQty()
            );
			
			$this->_coreSession->setProductOutBasket(json_encode($productOutBasket));
        }
    }

}