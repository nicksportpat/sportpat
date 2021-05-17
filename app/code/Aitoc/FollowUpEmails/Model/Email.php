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

use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailTableInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\Email as EmailsResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Email
 */
class Email extends AbstractModel implements EmailInterface, EmailTableInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(EmailsResourceModel::class);
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
     * @return int $campaignStepId
     */
    public function getCampaignStepId()
    {
        return $this->getData(self::COLUMN_NAME_CAMPAIGN_STEP_ID);
    }

    /**
     * @param int $campaignStepId
     *
     * @return $this
     */
    public function setCampaignStepId($campaignStepId)
    {
        return $this->setData(self::COLUMN_NAME_CAMPAIGN_STEP_ID, $campaignStepId);
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
     * @return string customer_email
     */
    public function getEmailAddress()
    {
        return $this->getData(self::COLUMN_NAME_CUSTOMER_EMAIL);
    }

    /**
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::COLUMN_NAME_CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * @return string created_at
     */
    public function getCreatedAt()
    {
        return $this->getData(self::COLUMN_NAME_CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::COLUMN_NAME_CREATED_AT, $createdAt);
    }

    /**
     * @return string scheduled_at
     */
    public function getScheduledAt()
    {
        return $this->getData(self::COLUMN_NAME_SCHEDULED_AT);
    }

    /**
     * @param string $scheduledAt
     * @return $this
     */
    public function setScheduledAt($scheduledAt)
    {
        return $this->setData(self::COLUMN_NAME_SCHEDULED_AT, $scheduledAt);
    }

    /**
     * @return string sent_at
     */
    public function getSentAt()
    {
        return $this->getData(self::COLUMN_NAME_SENT_AT);
    }

    /**
     * @param string $sentAt
     * @return $this
     */
    public function setSentAt($sentAt)
    {
        return $this->setData(self::COLUMN_NAME_SENT_AT, $sentAt);
    }

    /**
     * @return array
     */
    public function getEmailAttributes()
    {
        return $this->getData(self::COLUMN_NAME_EMAIL_ATTRIBUTES);
    }

    /**
     * @param array $attributes
     * @return EmailInterface
     */
    public function setEmailAttributes($attributes)
    {
        return $this->setData(self::COLUMN_NAME_EMAIL_ATTRIBUTES, $attributes);
    }

    /**
     * @return string unsubscribe_code
     */
    public function getSecretCode()
    {
        return $this->getData(self::COLUMN_NAME_SECRET_CODE);
    }

    /**
     * @param string $unsubscribeCode
     * @return $this
     */
    public function setSecretCode($unsubscribeCode)
    {
        return $this->setData(self::COLUMN_NAME_SECRET_CODE, $unsubscribeCode);
    }

    /**
     * @return string opened_at
     */
    public function getOpenedAt()
    {
        return $this->getData(self::COLUMN_NAME_OPENED_AT);
    }

    /**
     * @param string $openedAt
     * @return $this
     */
    public function setOpenedAt($openedAt)
    {
        return $this->setData(self::COLUMN_NAME_OPENED_AT, $openedAt);
    }

    /**
     * @return string transited_at
     */
    public function getTransitedAt()
    {
        return $this->getData(self::COLUMN_NAME_TRANSITED_AT);
    }

    /**
     * @param string $transitedAt
     * @return $this
     */
    public function setTransitedAt($transitedAt)
    {
        return $this->setData(self::COLUMN_NAME_TRANSITED_AT, $transitedAt);
    }
}
