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

use K3Live\ShopperApproved\Helper\Data;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Event\ObserverInterface;

class OrderCancelAfterObserver implements ObserverInterface
{
    private $curl;
    private $dataHelper;

    public function __construct(
        Curl $curl,
        Data $dataHelper
    ) {
        $this->curl = $curl;
        $this->dataHelper = $dataHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($observer && $this->dataHelper->getCancelReview()) {
            $order = $observer->getEvent()->getOrder();
            if ($order) {
                $orderId = $order->getIncrementId();
                $url = 'https://www.shopperapproved.com/api/review/'.
                '?siteid='.$this->dataHelper->getSiteId().
                '&token='.$this->dataHelper->getApiToken().
                '&cancel=1'.
                '&orderid='.$orderId;
                $this->curl->get($url);
                try {
                    $response = $this->curl->getBody();
                } catch (\Exception $e) {
                    \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->debug($e);
                }
            }
        }
    }
}
