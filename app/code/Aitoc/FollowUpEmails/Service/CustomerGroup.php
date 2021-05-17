<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Service;

use Aitoc\FollowUpEmails\Api\Service\CustomerGroupInterface;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroupCollection;

/**
 * Class CustomerGroup
 */
class CustomerGroup implements CustomerGroupInterface
{
    /**
     * @var CustomerGroupCollection
     */
    private $customerGroupCollection;

    /**
     * CustomerGroup constructor.
     * @param CustomerGroupCollection $customerGroupCollection
     */
    public function __construct(
        CustomerGroupCollection $customerGroupCollection
    ) {
        $this->customerGroupCollection = $customerGroupCollection;
    }

    /**
     * @return array
     */
    public function getCustomerGroupsIds()
    {
        $customerGroupIds = [];
        $customerGroups = $this->getCustomerGroupOptionArray();

        foreach ($customerGroups as $customerGroup) {
            $customerGroupIds[] = $customerGroup['value'];
        }

        return $customerGroupIds;
    }

    /**
     * @return array
     */
    private function getCustomerGroupOptionArray()
    {
        return $this->customerGroupCollection->toOptionArray();
    }
}
