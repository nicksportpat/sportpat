<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\TransitTo\Base;

use Aitoc\FollowUpEmails\Controller\TransitTo\Base\Homepage as BaseTransitToHomepageAction;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * Class Order
 */
abstract class Order extends BaseTransitToHomepageAction
{
    const REQUEST_PARAM_ORDER_ID = 'order_id';
    const ROUTE_NAME_SALES_ORDER_VIEW = 'sales/order/view';
    const ROUTE_PARAM_NAME_ORDER_ID = 'order_id';

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    protected function getResultRedirect(RequestInterface $request)
    {
        $orderId = $this->getRequestedOrderId($request);
        $requestedAnchor = $this->getRequestedAnchor($request);

        return $orderId
            ? $this->createRedirectToOrderById($orderId)
            : $this->createRedirectToHomepage($requestedAnchor);
    }

    /**
     * @param RequestInterface $request
     * @return int
     */
    protected function getRequestedOrderId(RequestInterface $request)
    {
        return (int) $request->getParam(self::REQUEST_PARAM_ORDER_ID);
    }

    /**
     * @param int $orderId
     */
    protected function createRedirectToOrderById($orderId)
    {
        $this->_redirect(self::ROUTE_NAME_SALES_ORDER_VIEW, [self::ROUTE_PARAM_NAME_ORDER_ID => $orderId]);
    }
}
