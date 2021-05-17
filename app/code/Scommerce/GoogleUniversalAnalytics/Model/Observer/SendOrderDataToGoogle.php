<?php
/**
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Scommerce\GoogleUniversalAnalytics\Model\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class SendOrderDataToGoogle implements ObserverInterface
{

    /**
     * @var \Scommerce\GoogleUniversalAnalytics\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_salesFactory;

    /**
     * @param \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper
     * @param \Magento\Sales\Model\OrderFactory $salesFactory
     */
    public function __construct(
        \Scommerce\GoogleUniversalAnalytics\Helper\Data $helper,
        \Magento\Sales\Model\OrderFactory $salesFactory
    )
    {
        $this->_helper = $helper;

        $this->_salesFactory = $salesFactory;
    }

    /**
     * Send order data after order placement using measurement protocol
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $storeId = $order->getStoreId();

        if (strlen($order->getStatus()) == 0 || !$order || $this->_helper->sendTransactionDataOnInvoice($storeId)) return;
        if (
            ((!$this->_helper->sendTransactionDataOffline($storeId) || $this->_helper->isAdmin()) &&
                (!$this->_helper->sendPhoneOrderTransaction($storeId) || !$this->_helper->isAdmin()))
            || (!$this->_helper->isEnhancedEcommerceEnabled($storeId))
        ) {
            $this->_helper->setSentDataFlag($order);
            return;
        }

        if (!($order->getSendDataToGoogle() == 1)) {
            $data = $this->_helper->addOrderData($order, $storeId, $this->_helper->getAccountId($storeId));
            $data = $this->_helper->addProductData($order, $data);
            $this->_helper->sendDataToGoogle($data);
            $this->_helper->setSentDataFlag($order);

            if (strlen($this->_helper->isLinkAccountsEnabled($storeId)) > 0) {
                $data = $this->_helper->addOrderData($order, $storeId, $this->_helper->getLinkedAccountId($storeId));
                $data = $this->_helper->addProductData($order, $data);
                $this->_helper->sendDataToGoogle($data);
            }
        }
    }


}