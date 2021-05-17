<?php
/**
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Scommerce\GoogleUniversalAnalytics\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class CreditMemoSaveAfter implements ObserverInterface
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
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
	 * @param \Magento\Framework\Session\SessionManagerInterface $coresession
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
								\Magento\Framework\Session\SessionManagerInterface $coresession,
                                \Magento\Framework\App\Request\Http $request,
                                \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper)
    {
        $this->_objectManager = $objectManager;
        $this->_coreSession = $coresession;
        $this->_request = $request;
        $this->_helper = $helper;
    }

    public function execute(EventObserver $observer)
    {
        if ($this->_helper->isEnhancedEcommerceEnabled()){
            $creditMemo = $observer->getEvent()->getCreditmemo();
            $order = $creditMemo->getOrder();
            if (strlen($order->getGoogleCookie())==0) return;

            $storeId= $order->getStoreId();
            $orderId = $order->getIncrementId();
            $products = array();
            $fullRefund = false;

            if (count($order->getAllItems())==count($creditMemo->getAllItems())){
                $fullRefund = true;
            }

            if ($fullRefund==false){
                foreach ($creditMemo->getAllItems() as $item) {
                    if($item->getBasePrice()<=0) continue;
                    $products[] = array('id' => $item->getSku(), 'qty' => $item->getQty());
                }
            }

            $response = array(
                'orderId'   => $orderId,
                'storeId'   => $storeId,
                'products'  => $products,
                'fullRefund'=> $fullRefund,
            );
			
			$this->_coreSession->setRefundOrder(json_encode($response));
        }
    }

}