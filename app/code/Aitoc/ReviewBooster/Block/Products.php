<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Block;

use Aitoc\FollowUpEmails\Block\Products as BaseProducts;
use Magento\Catalog\Api\Data\ProductInterface;
use Aitoc\FollowUpEmails\Api\Contoller\Account\Login\RequestParamNameInterface;

/**
 * Class Products
 */
class Products extends BaseProducts
{
    /**
     * @param ProductInterface $product
     * @return array
     */
    protected function getRouteParamsByProduct(ProductInterface $product)
    {
        $routeParams = parent::getRouteParamsByProduct($product);

        $routeParams[RequestParamNameInterface::ANCHOR] = 'review-form';

        return $routeParams;
    }
}
