<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Model;

use Aitoc\FollowUpEmails\Api\CampaignProviderInterface;
use Aitoc\FollowUpEmails\Api\CampaignRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Helper\SearchCriteriaBuilderInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignTableInterface;
use Magento\Framework\Api\SearchCriteria;

/**
 * Class CampaignProvider
 */
class CampaignProvider implements CampaignProviderInterface
{
    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * @var SearchCriteriaBuilderInterface
     */
    private $searchCriteriaBuilderHelper;

    /**
     * CampaignProvider constructor.
     * @param CampaignRepositoryInterface $campaignRepository
     * @param SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper
     */
    public function __construct(
        CampaignRepositoryInterface $campaignRepository,
        SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper
    ) {
        $this->campaignRepository = $campaignRepository;
        $this->searchCriteriaBuilderHelper = $searchCriteriaBuilderHelper;
    }

    /**
     * @param string $eventCode
     * @return CampaignInterface[]
     */
    public function getCampaignsByEventCode($eventCode)
    {
        $filters = [
            [CampaignTableInterface::COLUMN_NAME_EVENT_CODE, $eventCode],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);

        return $this->getCampaignsBySearchCriteria($searchCriteria);
    }

    /**
     * @param array $filters
     * @return SearchCriteria
     */
    private function createSearchCriteria($filters)
    {
        return $this->searchCriteriaBuilderHelper->createSearchCriteria($filters);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return CampaignInterface[]
     */
    private function getCampaignsBySearchCriteria(SearchCriteria $searchCriteria)
    {
        $campaignSearchResults = $this->getCampaignsSearchResults($searchCriteria);

        return $campaignSearchResults->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return CampaignSearchResultsInterface
     */
    private function getCampaignsSearchResults(SearchCriteria $searchCriteria)
    {
        return $campaignsList = $this->campaignRepository->getList($searchCriteria);
    }
}
