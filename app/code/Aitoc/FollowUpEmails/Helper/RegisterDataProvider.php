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

use Aitoc\FollowUpEmails\Api\RegisterDataProviderInterface;
use Magento\Framework\Registry;

/**
 * Class RegisterDataProvider
 */
class RegisterDataProvider implements RegisterDataProviderInterface
{
    protected $registry;

    /**
     * RegisterDataProvider constructor.
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param string $eventCode
     * @return self
     */
    public function setCurrentEventCode($eventCode)
    {
        return $this->setToRegistry(self::CURRENT_EVENT_CODE, $eventCode);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    protected function setToRegistry($key, $value)
    {
        $this->registry->register($key, $value);

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentEventCode()
    {
        return $this->getFromRegistry(self::CURRENT_EVENT_CODE);
    }

    /**
     * @param string $key
     * @return mixed
     */
    protected function getFromRegistry($key)
    {
        return $this->registry->registry($key);
    }

    /**
     * @inheritDoc
     */
    public function getCurrentCampaignId()
    {
        return $this->getFromRegistry(self::CURRENT_CAMPAIGN_ID);
    }

    /**
     * @inheritDoc
     */
    public function setCurrentCampaignId($campaignId)
    {
        return $this->setToRegistry(self::CURRENT_CAMPAIGN_ID, $campaignId);
    }
}
