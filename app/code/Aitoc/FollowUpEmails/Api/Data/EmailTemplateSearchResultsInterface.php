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
use Magento\Framework\Mail\TemplateInterface;

/**
 * Interface CampaignsSearchResultsInterface
 */
interface EmailTemplateSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return TemplateInterface[]
     */
    public function getItems();

    /**
     * @param TemplateInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
