<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api;

use Aitoc\FollowUpEmails\Api\Helper\EventEmailsGeneratorHelperInterface;

/**
 * Interface for managing events.
 * @api
 */
interface EventManagementInterface
{
    /**
     * @param string $eventCode
     * @return array
     */
    public function getAttributesByEventCode($eventCode);

    /**
     * @return array
     */
    public function getAllEvents();

    /**
     * @return string[]
     */
    public function getActiveEventsCodes();

    /**
     * @return array
     */
    public function getActiveEvents();

    /**
     * @param string $eventCode
     * @return EventEmailsGeneratorHelperInterface
     */
    public function getEventEmailGeneratorHelperByEventCode($eventCode);

    /**
     * @param string $eventCode
     * @return bool
     */
    public function isEventEnabled($eventCode);

    /**
     * @param string $eventCode
     * @return string
     */
    public function getEventNameByCode($eventCode);
}
