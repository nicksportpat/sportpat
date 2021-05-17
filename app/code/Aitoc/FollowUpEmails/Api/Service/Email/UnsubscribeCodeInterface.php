<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Service\Email;

/**
 * Class UnsubscribeCode
 */
interface UnsubscribeCodeInterface
{
    /**
     * @return string
     */
    public function generateUnsubscribeCode();
}
