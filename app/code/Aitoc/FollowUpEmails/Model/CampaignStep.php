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

use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignStepTableInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\CampaignStep as CampaignStepsResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CampaignStep
 */
class CampaignStep extends AbstractModel implements CampaignStepInterface, CampaignStepTableInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(CampaignStepsResourceModel::class);
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
     * @return string name
     */
    public function getName()
    {
        return $this->getData(self::COLUMN_NAME_NAME);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::COLUMN_NAME_NAME, $name);
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
     * @return string template_id
     */
    public function getTemplateId()
    {
        return $this->getData(self::COLUMN_NAME_TEMPLATE_ID);
    }

    /**
     * @param string $templateId
     *
     * @return $this
     */
    public function setTemplateId($templateId)
    {
        return $this->setData(self::COLUMN_NAME_TEMPLATE_ID, $templateId);
    }

    /**
     * @return int delay_period
     */
    public function getDelayPeriod()
    {
        return $this->getData(self::COLUMN_NAME_DELAY_PERIOD);
    }

    /**
     * @param int $delayPeriod
     *
     * @return $this
     */
    public function setDelayPeriod($delayPeriod)
    {
        return $this->setData(self::COLUMN_NAME_DELAY_PERIOD, $delayPeriod);
    }

    /**
     * @return int status
     */
    public function getStatus()
    {
        return $this->getData(self::COLUMN_NAME_STATUS);
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::COLUMN_NAME_STATUS, $status);
    }

    /**
     * @return string unit
     */
    public function getDelayUnit()
    {
        return $this->getData(self::COLUMN_NAME_DELAY_UNIT);
    }

    /**
     * @param string $unit
     * @return $this
     */
    public function setDelayUnit($unit)
    {
        return $this->setData(self::COLUMN_NAME_DELAY_UNIT, $unit);
    }

    /**
     * @return int discount_status
     */
    public function getDiscountStatus()
    {
        return $this->getData(self::COLUMN_NAME_DISCOUNT_STATUS);
    }

    /**
     * @param int $discountStatus
     * @return $this
     */
    public function setDiscountStatus($discountStatus)
    {
        return $this->setData(self::COLUMN_NAME_DISCOUNT_STATUS, $discountStatus);
    }

    /**
     * @return int discount_percent
     */
    public function getDiscountPercent()
    {
        return $this->getData(self::COLUMN_NAME_DISCOUNT_PERCENT);
    }

    /**
     * @param int $discountPercent
     * @return $this
     */
    public function setDiscountPercent($discountPercent)
    {
        return $this->setData(self::COLUMN_NAME_DISCOUNT_PERCENT, $discountPercent);
    }

    /**
     * @return int discount_period
     */
    public function getDiscountPeriod()
    {
        return $this->getData(self::COLUMN_NAME_DISCOUNT_PERIOD);
    }

    /**
     * @param int $discountPeriod
     * @return $this
     */
    public function setDiscountPeriod($discountPeriod)
    {
        return $this->setData(self::COLUMN_NAME_DISCOUNT_PERIOD, $discountPeriod);
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $options = $this->getData(self::COLUMN_NAME_OPTIONS);

        if (is_string($options)) {
            $options = json_decode($options, true);
        }

        return is_array($options) ? $options : [];
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        return $this->setData(self::COLUMN_NAME_OPTIONS, json_encode($options));
    }
}
