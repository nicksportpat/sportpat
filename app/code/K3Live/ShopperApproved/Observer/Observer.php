<?php

/**
 * K3Live Module for ShopperApproved
 *
 * @package   ShopperApproved
 * @author    K3Live <support@k3live.com>
 * @copyright 2018 Copyright (c) Woopra (http://www.k3live.com/)
 * @license   Open Software License (OSL 3.0)
 */

namespace K3Live\ShopperApproved\Observer;

use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class Observer implements ObserverInterface
{
    private $checkoutSession;
    private $customerSession;
    private $order;

    public function __construct(
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        Order $order
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->order = $order;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!empty($observer)) {
            $lastOrderId = $this->checkoutSession->getLastRealOrderId();
            if (!empty($lastOrderId)) {
                $order = $this->order->loadByIncrementId($lastOrderId);
                $this->customerSession->setData('sa_checkout_success_order_id', $lastOrderId);
            }
        }
    }
}
