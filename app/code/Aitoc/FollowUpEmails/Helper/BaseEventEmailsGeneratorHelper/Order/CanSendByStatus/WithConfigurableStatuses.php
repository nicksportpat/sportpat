<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper\BaseEventEmailsGeneratorHelper\Order\CanSendByStatus;

use Aitoc\FollowUpEmails\Helper\BaseEventEmailsGeneratorHelper\Order\CanSendByStatus as CanSendByStatusEmailGeneratorHelper;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class WithConfigurableStatuses
 *
 * Configurable should save order status in email attributes.
 */
abstract class WithConfigurableStatuses extends CanSendByStatusEmailGeneratorHelper
{
    /**
     * @param OrderInterface $entity
     * @return array
     */
    public function getEmailAttributesByEntity($entity)
    {
        $attributes = parent::getEmailAttributesByEntity($entity);
        $attributes[self::ATTRIBUTE_CODE_ORDER_STATUS] = $entity->getStatus();

        return $attributes;
    }
}
