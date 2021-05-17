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

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface UnsubscribedListSearchResultsInterface
 */
interface UnsubscribedEmailAddressSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return UnsubscribedEmailAddressInterface[]
     */
    public function getItems();

    /**
     * @param UnsubscribedEmailAddressInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
