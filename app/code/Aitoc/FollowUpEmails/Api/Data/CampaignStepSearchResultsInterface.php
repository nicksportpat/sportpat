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
 * Interface CampaignStepsSearchResultsInterface
 */
interface CampaignStepSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return CampaignStepInterface[]
     */
    public function getItems();

    /**
     * @param CampaignStepInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
