<?php
/**
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Scommerce\GoogleUniversalAnalytics\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class CheckoutCartAddAfter implements ObserverInterface
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
     * @var \Magento\Wishlist\Model\ItemFactory
     */
    protected $_itemFactory;
	
	/**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
	protected $_coreSession;
	
	/**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    protected $_cookie;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
	 * @param \Magento\Framework\Session\SessionManagerInterface $coresession
     * @param \Magento\Framework\App\Request\Http $request
	 * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookie
     * @param \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper
     * @param \Magento\Wishlist\Model\ItemFactory $itemFactory
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectmanager,
								\Magento\Framework\Session\SessionManagerInterface $coresession,
                                \Magento\Framework\App\Request\Http $request,
								\Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookie,
                                \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper,
                                \Magento\Wishlist\Model\ItemFactory $itemFactory)
    {
        $this->_objectManager = $objectmanager;
		$this->_coreSession = $coresession;
        $this->_request = $request;
		$this->_cookie = $cookie;
        $this->_helper = $helper;
        $this->_itemFactory = $itemFactory;
    }

    public function execute(EventObserver $observer)
    {
        if ($this->_helper->isEnhancedEcommerceEnabled()){
            $event = $observer->getEvent();
			$quoteItem = $event->getQuoteItem();
		
			$productId = $this->_request->getParam('product', 0);
            $qty = $this->_request->getParam('qty', 1);
            if ($productId==0){
                $itemId = $this->_request->getParam('item', 0);
                if ($itemId>0){
                    $wishlist = $this->_objectManager->create('\Magento\Wishlist\Model\Wishlist')->load($itemId);
                    if ($wishlist){
                        $productId = $wishlist->getProductId();
                    }
                }
            }
			
            $product = $this->_objectManager->create('\Magento\Catalog\Model\Product')
                            ->load($productId);
            if (!$product->getId()) {
                return;
            }
			
			$category = str_replace('"','',$this->_cookie->getCookie("googlecategory"));
		
			if (!isset($category)){
				$category = $this->_helper->getProductCategoryName($product);
			}
			
            $productToBasket = array(
                'id' => $product->getSku(),
                'name' => $product->getName(),
                'category' => $category,
                'brand' => $this->_helper->getBrand($product),
                'price' => $product->getFinalPrice(),
                'qty'=> $quoteItem->getQty()
            );

            $this->_coreSession->setProductToBasket(json_encode($productToBasket));
        }
    }

}