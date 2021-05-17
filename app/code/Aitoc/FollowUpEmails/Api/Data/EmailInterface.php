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
 * Interface EmailsInterface
 */
interface EmailInterface
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
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

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
     * Returns created_at field
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Returns scheduled_at field
     *
     * @return string
     */
    public function getScheduledAt();

    /**
     * @param string $scheduledAt
     * @return $this
     */
    public function setScheduledAt($scheduledAt);

    /**
     * Returns created_at field
     *
     * @return string
     */
    public function getSentAt();

    /**
     * @param string $sentAt
     * @return $this
     */
    public function setSentAt($sentAt);

    /**
     * Returns customer_email field
     *
     * @return string
     */
    public function getEmailAddress();

    /**
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Returns unsubscribe_code field
     *
     * @return string
     */
    public function getSecretCode();

    /**
     * @param string $unsubscribeCode
     * @return $this
     */
    public function setSecretCode($unsubscribeCode);

    /**
     * Returns opened_at field
     *
     * @return string
     */
    public function getOpenedAt();

    /**
     * @param string $openedAt
     * @return $this
     */
    public function setOpenedAt($openedAt);

    /**
     * Returns transited_at field
     *
     * @return string
     */
    public function getTransitedAt();

    /**
     * @param string $transitedAt
     * @return $this
     */
    public function setTransitedAt($transitedAt);

    /**
     * @return array
     */
    public function getEmailAttributes();

    /**
     * @param array $attributes
     * @return $this
     */
    public function setEmailAttributes($attributes);
}
