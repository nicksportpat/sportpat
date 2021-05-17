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

use Magento\Framework\Api\SearchCriteria;

/**
 * Class SearchCriteriaBuilder
 */
interface SearchCriteriaBuilderInterface
{
    /**
     * @param array $filters
     * @param array $sortOrders
     * @return SearchCriteria
     */
    public function createSearchCriteria($filters = [], $sortOrders = []);
}
