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

use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\UnsubscribedEmailAddressTableInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\UnsubscribedEmailAddress as UnsubscribedListResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * Class UnsubscribedEmailAddress
 */
class UnsubscribedEmailAddress extends AbstractModel implements UnsubscribedEmailAddressInterface, UnsubscribedEmailAddressTableInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(UnsubscribedListResourceModel::class);
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
     * @return string customer_email
     */
    public function getCustomerEmail()
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
     * @return int email_id
     */
    public function getEmailId()
    {
        return $this->getData(self::COLUMN_NAME_EMAIL_ID);
    }

    /**
     * @param int $emailId
     *
     * @return $this
     */
    public function setEmailId($emailId)
    {
        return $this->setData(self::COLUMN_NAME_EMAIL_ID, $emailId);
    }
}
