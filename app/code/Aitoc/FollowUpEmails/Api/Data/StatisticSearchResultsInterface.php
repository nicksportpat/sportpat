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
 * Interface StatisticsSearchResultsInterface
 */
interface StatisticSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return StatisticInterface[]
     */
    public function getItems();

    /**
     * @param StatisticInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
