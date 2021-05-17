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

use Aitoc\FollowUpEmails\Controller\TransitTo\Base as BaseTransitToAction;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

/**
 * Class Cart
 */
abstract class Cart extends BaseTransitToAction
{
    const ROUTE_PATH_CHECKOUT_CART = 'checkout/cart';

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    protected function getResultRedirect(RequestInterface $request)
    {
        return $this->redirectToCheckoutCart();
    }

    /**
     * @return ResponseInterface
     */
    private function redirectToCheckoutCart()
    {
        return $this->_redirect(self::ROUTE_PATH_CHECKOUT_CART);
    }
}
