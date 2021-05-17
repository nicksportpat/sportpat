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

use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface EmailRepositoryInterface
 */
interface EmailRepositoryInterface
{
    /**
     * @param EmailInterface $emailModel
     * @return EmailInterface
     */
    public function save(EmailInterface $emailModel);

    /**
     * @param int $entityId
     * @return EmailInterface
     */
    public function get($entityId);

    /**
     * @param string $unsubscribeCode
     * @return EmailInterface|null
     */
    public function getByUnsubscribeCode($unsubscribeCode);

    /**
     * @param EmailInterface $emailsModel
     * @return bool
     */
    public function delete(EmailInterface $emailsModel);

    /**
     * @param int $entityId
     * @return bool
     */
    public function deleteById($entityId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return EmailSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
