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

use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignTableInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\Campaign as CampaignsResourceModel;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Campaigns
 */
class Campaign extends AbstractModel implements CampaignInterface, CampaignTableInterface
{
    /**
     * Init resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(CampaignsResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->getData(self::COLUMN_NAME_ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::COLUMN_NAME_ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData(self::COLUMN_NAME_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        return $this->setData(self::COLUMN_NAME_NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->getData(self::COLUMN_NAME_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setDescription($description)
    {
        return $this->setData(self::COLUMN_NAME_DESCRIPTION, $description);
    }

    /**
     * @inheritdoc
     */
    public function getEventCode()
    {
        return $this->getData(self::COLUMN_NAME_EVENT_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setEventCode($eventCode)
    {
        return $this->setData(self::COLUMN_NAME_EVENT_CODE, $eventCode);
    }

    /**
     * @inheritdoc
     */
    public function getSender()
    {
        return $this->getData(self::COLUMN_NAME_SENDER);
    }

    /**
     * @inheritdoc
     */
    public function setSender($sender)
    {
        return $this->setData(self::COLUMN_NAME_SENDER, $sender);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData(self::COLUMN_NAME_STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::COLUMN_NAME_STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerGroupIds()
    {
        return $this->getCommaSeparatedStringFieldAsArray(self::COLUMN_NAME_CUSTOMER_GROUP_IDS);
    }

    /**
     * @param string $fieldName
     * @return array
     */
    protected function getCommaSeparatedStringFieldAsArray($fieldName)
    {
        return ($idsString = $this->getData($fieldName))
            ? explode(",", $idsString)
            : [];
    }

    /**
     * @inheritdoc
     */
    public function setCustomerGroupIds($customerGroupIds)
    {
        return $this->setArrayAsCommaSeparatedString(self::COLUMN_NAME_CUSTOMER_GROUP_IDS, $customerGroupIds);
    }

    /**
     * @param string $fieldName
     * @param array $values
     * @return Campaign
     */
    protected function setArrayAsCommaSeparatedString($fieldName, $values)
    {
        $valuesString = implode(",", $values);

        return $this->setData($fieldName, $valuesString);
    }

    /**
     * @inheritdoc
     */
    public function getStoreIds()
    {
        return $this->getCommaSeparatedStringFieldAsArray(self::COLUMN_NAME_STORE_IDS);
    }

    /**
     * @inheritdoc
     */
    public function setStoreIds($storeIds)
    {
        return $this->setArrayAsCommaSeparatedString(self::COLUMN_NAME_STORE_IDS, $storeIds);
    }
}
