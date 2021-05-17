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

use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressInterface;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Interface UnsubscribedEmailAddressRepositoryInterface
 */
interface UnsubscribedEmailAddressRepositoryInterface
{
    /**
     * @return UnsubscribedEmailAddressInterface
     */
    public function create();

    /**
     * @param UnsubscribedEmailAddressInterface $unsubscribedListModel
     * @return UnsubscribedEmailAddressInterface
     */
    public function save(UnsubscribedEmailAddressInterface $unsubscribedListModel);

    /**
     * @param int $entityId
     * @return UnsubscribedEmailAddressInterface
     */
    public function get($entityId);

    /**
     * @param string $emailAddress
     * @return UnsubscribedEmailAddressInterface[]
     */
    public function getByEmailAddress($emailAddress);

    /**
     * @param UnsubscribedEmailAddressInterface $unsubscribedListModel
     * @return bool
     */
    public function delete(UnsubscribedEmailAddressInterface $unsubscribedListModel);

    /**
     * @param int $entityId
     * @return bool
     */
    public function deleteById($entityId);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return UnsubscribedEmailAddressSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
