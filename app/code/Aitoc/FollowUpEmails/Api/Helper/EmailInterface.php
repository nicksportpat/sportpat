<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Api\Helper;

/**
 * Class Email
 */
interface EmailInterface
{
    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param array $emailAttributes
     * @return int
     */
    public function getStoreIdByEmailAttributes(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        $emailAttributes
    );

        /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param array $emailAttributes
     * @return mixed
     */
    public function getEntityByEmailAttributes(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        $emailAttributes
    );
}
