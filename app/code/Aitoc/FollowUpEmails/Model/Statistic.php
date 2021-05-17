<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Model;

use Aitoc\FollowUpEmails\Api\Data\StatisticInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\StatisticTableInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\Statistic as StatisticsResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Statistic
 */
class Statistic extends AbstractModel implements StatisticInterface, StatisticTableInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(StatisticsResourceModel::class);
    }

    /**
     * @return int entity_id
     */
    public function getEntityId()
    {
        return $this->getData(self::COLUMN_NAME_ENTITY_ID);
    }

    /**
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::COLUMN_NAME_ENTITY_ID, $entityId);
    }

    /**
     * @return int campaignId
     */
    public function getCampaignId()
    {
        return $this->getData(self::COLUMN_NAME_CAMPAIGN_ID);
    }

    /**
     * @param int $campaignId
     *
     * @return $this
     */
    public function setCampaignId($campaignId)
    {
        return $this->setData(self::COLUMN_NAME_CAMPAIGN_ID, $campaignId);
    }

    /**
     * @return int campaign_step_id
     */
    public function getCampaignStepId()
    {
        return $this->getData(self::COLUMN_NAME_CAMPAIGN_STEP_ID);
    }

    /**
     * @param int $campaignStepId
     * @return $this
     */
    public function setCampaignStepId($campaignStepId)
    {
        return $this->setData(self::COLUMN_NAME_CAMPAIGN_STEP_ID, $campaignStepId);
    }

    /**
     * @return string key
     */
    public function getKey()
    {
        return $this->getData(self::COLUMN_NAME_KEY);
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        return $this->setData(self::COLUMN_NAME_KEY, $key);
    }

    /**
     * @return string value
     */
    public function getValue()
    {
        return $this->getData(self::COLUMN_NAME_VALUE);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->setData(self::COLUMN_NAME_VALUE, $value);
    }

    /**
     * @return string updated_at
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::COLUMN_NAME_UPDATED_AT);
    }

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::COLUMN_NAME_UPDATED_AT, $updatedAt);
    }

    /**
     * @return string event_code
     */
    public function getEventCode()
    {
        return $this->getData(self::COLUMN_NAME_EVENT_CODE);
    }

    /**
     * @param string $eventCode
     * @return $this
     */
    public function setEventCode($eventCode)
    {
        return $this->setData(self::COLUMN_NAME_EVENT_CODE, $eventCode);
    }
}
