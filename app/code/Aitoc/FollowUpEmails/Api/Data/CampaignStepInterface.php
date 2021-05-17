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
 * Interface CampaignStepsInterface
 */
interface CampaignStepInterface
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
     * Returns template_id field
     *
     * @return string
     */
    public function getTemplateId();

    /**
     * @param string $templateId
     *
     * @return $this
     */
    public function setTemplateId($templateId);

    /**
     * Returns delay_period field
     *
     * @return int
     */
    public function getDelayPeriod();

    /**
     * @param int $delayPeriod
     *
     * @return $this
     */
    public function setDelayPeriod($delayPeriod);

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
     * @return string
     */
    public function getDelayUnit();

    /**
     * @param string $unit
     * @return $this
     */
    public function setDelayUnit($unit);

    /**
     * @return int
     */
    public function getDiscountStatus();

    /**
     * @param int $discountStatus
     * @return $this
     */
    public function setDiscountStatus($discountStatus);

    /**
     * @return int
     */
    public function getDiscountPercent();

    /**
     * @param int $discountPercent
     * @return $this
     */
    public function setDiscountPercent($discountPercent);

    /**
     * @return int
     */
    public function getDiscountPeriod();

    /**
     * @param int $discountPeriod
     * @return $this
     */
    public function setDiscountPeriod($discountPeriod);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options);
}
