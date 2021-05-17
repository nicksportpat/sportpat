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
 * Interface StatisticInterface
 */
interface StatisticInterface
{
    /**
     * Constants for statistic status
     */
    const SENT = 'sent';
    const OPENED = 'opened';
    const TRANSITED = 'transited';
    const UNSUBSCRIBED = 'unsubscribed';
    const SALES = 'sales';

    /**
     * Constants for statistic period
     */
    const WEEK = 'week';
    const MONTH = 'month';
    const HALF_YEAR = 'half_year';
    const YEAR = 'year';

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
     * Returns campaign_id field
     *
     * @return int
     */
    public function getCampaignId();

    /**
     * @param int $campaignId
     *
     * @return $this
     */
    public function setCampaignId($campaignId);

    /**
     * Returns campaign_step_id field
     *
     * @return int
     */
    public function getCampaignStepId();

    /**
     * @param int $campaignStepId
     *
     * @return $this
     */
    public function setCampaignStepId($campaignStepId);

    /**
     * Returns key field
     *
     * @return string
     */
    public function getKey();

    /**
     * @param string $key
     * @return $this
     */
    public function setKey($key);

    /**
     * Returns value field
     *
     * @return int
     */
    public function getValue();

    /**
     * @param int $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Returns updated_at field
     *
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return string
     */
    public function getEventCode();

    /**
     * @param string $eventCode
     * @return $this
     */
    public function setEventCode($eventCode);
}
