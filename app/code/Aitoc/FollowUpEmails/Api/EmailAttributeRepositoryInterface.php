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

use Aitoc\FollowUpEmails\Api\Data\EmailAttributeInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailAttributeSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface EmailAttributeRepositoryInterface
 */
interface EmailAttributeRepositoryInterface
{
    /**
     * @param EmailAttributeInterface $emailAttributesModel
     * @return EmailAttributeInterface
     */
    public function save(EmailAttributeInterface $emailAttributesModel);

    /**
     * @param int $entityId
     * @return EmailAttributeInterface
     */
    public function get($entityId);

    /**
     * @param EmailAttributeInterface $emailAttributesModel
     * @return bool
     */
    public function delete(EmailAttributeInterface $emailAttributesModel);

    /**
     * @param int $entityId
     * @return bool
     */
    public function deleteById($entityId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return EmailAttributeSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param array $filters
     * @return EmailAttributeInterface[]
     */
    public function getByFilters($filters);
}
