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

use Aitoc\FollowUpEmails\Api\Data\EmailAttributeInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailAttributeTableInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\EmailAttribute as EmailAttributesResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * Class EmailAttribute
 */
class EmailAttribute extends AbstractModel implements EmailAttributeInterface, EmailAttributeTableInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(EmailAttributesResourceModel::class);
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

    /**
     * @return string attribute_code
     */
    public function getAttributeCode()
    {
        return $this->getData(self::COLUMN_NAME_ATTRIBUTE_CODE);
    }

    /**
     * @param string $attributeCode
     * @return $this
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->setData(self::COLUMN_NAME_ATTRIBUTE_CODE, $attributeCode);
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
}
