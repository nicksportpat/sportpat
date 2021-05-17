<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper\BaseEventEmailsGeneratorHelper\Order;

use Aitoc\FollowUpEmails\Helper\BaseEventEmailsGeneratorHelper\Order;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;

/**
 * Class CanSendByStatus
 */
abstract class CanSendByStatus extends Order
{
    /**
     * @param CampaignStepInterface $campaignStep
     * @param array $emailAttributes
     * @return bool
     */
    public function canSendEmail(CampaignStepInterface $campaignStep, $emailAttributes)
    {
        $order = $this->getEntityByEmailAttributes($emailAttributes);
        $orderStatus = $order->getStatus();
        $allowedStatuses = $this->getAllowedOrderStatuses($campaignStep);

        return in_array($orderStatus, $allowedStatuses);
    }
}
