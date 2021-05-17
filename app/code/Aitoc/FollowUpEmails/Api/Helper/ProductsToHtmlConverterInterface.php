<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Helper;


interface ProductsToHtmlConverterInterface
{
    /**
     * @param array $products
     * @param int $emailId
     * @return string
     */
    public function getProductsHtml($products, $emailId);
}
