<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper;

use Magento\Customer\Model\Customer;
use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class CustomersToCustomerInterfacesConverter
 */
class CustomersToCustomerInterfacesConverter
{
    /**
     * @param Customer[] $customers
     * @return CustomerInterface[]
     */
    public function convert($customers)
    {
        $dataCustomers = [];

        foreach ($customers as $customer) {
            $customerId = $customer->getId();
            $dataCustomers[$customerId] = $customer->getDataModel();
        }

        return $dataCustomers;
    }

}
