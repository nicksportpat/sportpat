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
 * Interface BackendTemplateHelperInterface
 */
interface BackendTemplateHelperInterface
{
    /**
     * @param string $templateCode
     * @param string $templateName
     * @param string $styles
     * @return int Backend Email Template Id
     */
    public function createAndSaveByCodeAndName($templateCode, $templateName, $styles = null);
}
