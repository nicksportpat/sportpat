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

use Aitoc\FollowUpEmails\Api\Data\EmailTemplateSearchResultsInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Mail\TemplateInterface;

/**
 * Interface EmailTemplateRepositoryInterface
 */
interface EmailTemplateRepositoryInterface
{
    /**
     * @param int $emailTemplateId
     * @return TemplateInterface
     */
    public function get($emailTemplateId);

    /**
     * @param TemplateInterface $emailTemplate
     * @return TemplateInterface
     */
    public function save(TemplateInterface $emailTemplate);

    /**
     * @param SearchCriteria $searchCriteria
     * @return EmailTemplateSearchResultsInterface
     */
    public function getList(SearchCriteria $searchCriteria);
}
