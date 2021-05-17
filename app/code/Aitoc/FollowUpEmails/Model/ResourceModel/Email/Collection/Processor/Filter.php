<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Model\ResourceModel\Email\Collection\Processor;

use Aitoc\FollowUpEmails\Model\ResourceModel\Email\Collection\Processor\Filter\OrderId as OrderIdProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessor\FilterProcessor;

/**
 * Class EmailJoinProcessor
 */
class Filter extends FilterProcessor
{
    /**
     * Filter constructor.
     * @param OrderIdProcessor $orderIdProcessor
     * @param array $fieldMapping
     */
    public function __construct(OrderIdProcessor $orderIdProcessor, array $fieldMapping = [])
    {
        $customFilters = [
            'order_id' => $orderIdProcessor
        ];

        parent::__construct($customFilters, $fieldMapping);
    }
}
