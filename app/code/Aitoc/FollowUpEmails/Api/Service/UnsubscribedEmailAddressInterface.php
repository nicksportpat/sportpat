<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Service;

/**
 * Class UnsubscribedEmailAddress
 */
interface UnsubscribedEmailAddressInterface
{
    /**
     * @param string $emailAddress
     * @param string[] $newUnsubscribedEventsCodes
     * @param int|null $emailId
     */
    public function updateUnsubscribedEventsForEmail($emailAddress, $newUnsubscribedEventsCodes, $emailId = null);
}
