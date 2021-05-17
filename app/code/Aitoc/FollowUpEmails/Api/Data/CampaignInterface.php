<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Api\Data;

/**
 * Interface CampaignInterface
 */
interface CampaignInterface
{
    /**
     * Returns entity_id field
     *
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getEventCode();

    /**
     * @param string $eventCode
     * @return $this
     */
    public function setEventCode($eventCode);

    /**
     * @return string
     */
    public function getSender();

    /**
     * @param string $sender
     * @return $this
     */
    public function setSender($sender);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Returns string
     *
     * @return array
     */
    public function getCustomerGroupIds();

    /**
     * @param int[] $customerGroupIds
     *
     * @return $this
     */
    public function setCustomerGroupIds($customerGroupIds);

    /**
     * Returns string
     *
     * @return array
     */
    public function getStoreIds();

    /**
     * @param int[] $storeIds
     *
     * @return $this
     */
    public function setStoreIds($storeIds);

    /**
     * @param array $arr
     * @return $this
     */
    public function addData(array $arr);

    /**
     * @return array
     */
    public function getData();
}
