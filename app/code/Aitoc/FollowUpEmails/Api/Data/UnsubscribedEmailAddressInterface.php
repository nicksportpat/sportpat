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
 * Interface UnsubscribedEmailAddressInterface
 */
interface UnsubscribedEmailAddressInterface
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
     * Returns customer_email field
     *
     * @return string
     */
    public function getCustomerEmail();

    /**
     * @param string $customerEmail
     * @return $this
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Returns event_code field
     *
     * @return string
     */
    public function getEventCode();

    /**
     * @param string $eventCode
     * @return $this
     */
    public function setEventCode($eventCode);

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
     * Returns email_id field
     *
     * @return int
     */
    public function getEmailId();

    /**
     * @param int $emailId
     *
     * @return $this
     */
    public function setEmailId($emailId);
}
