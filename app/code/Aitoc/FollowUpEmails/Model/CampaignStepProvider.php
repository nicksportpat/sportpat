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

use Aitoc\FollowUpEmails\Api\CampaignStepProviderInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Helper\SearchCriteriaBuilderInterface as SearchCriteriaBuilderHelperInterface;
use Magento\Framework\Api\SearchCriteria;
use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignStepTableInterface;

/**
 * Class CampaignStepsProvider
 */
class CampaignStepProvider implements CampaignStepProviderInterface
{
    /**
     * @var SearchCriteriaBuilderHelperInterface
     */
    private $searchCriteriaBuilderHelper;

    /**
     * @var CampaignStepRepository
     */
    private $campaignStepRepository;

    /**
     * CampaignStepsProvider constructor.
     * @param SearchCriteriaBuilderHelperInterface $searchCriteriaBuilderHelper
     * @param CampaignStepRepository $campaignStepRepository
     */
    public function __construct(
        SearchCriteriaBuilderHelperInterface $searchCriteriaBuilderHelper,
        CampaignStepRepository $campaignStepRepository
    ) {
        $this->searchCriteriaBuilderHelper = $searchCriteriaBuilderHelper;
        $this->campaignStepRepository = $campaignStepRepository;
    }

    /**
     * @param CampaignInterface $campaign
     * @return CampaignStepInterface[]
     */
    public function getCampaignStepsByCampaign(CampaignInterface $campaign)
    {
        $campaignId = $campaign->getEntityId();

        return $this->getCampaignStepsByCampaignId($campaignId);
    }

    /**
     * @param int $campaignId
     * @return CampaignStepInterface[]
     */
    public function getCampaignStepsByCampaignId($campaignId)
    {
        $filters = [
            [CampaignStepTableInterface::COLUMN_NAME_CAMPAIGN_ID, $campaignId],
        ];

        $searchCriteria = $this->createSearchCriteria($filters);

        return $this->getCampaignStepsBySearchCriteria($searchCriteria);
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
     * @return CampaignStepInterface[]
     */
    private function getCampaignStepsBySearchCriteria(SearchCriteria $searchCriteria)
    {
        $campaignStepSearchResult = $this->getCampaignStepSearchResults($searchCriteria);

        return $campaignStepSearchResult->getItems();
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return CampaignStepSearchResultsInterface
     */
    private function getCampaignStepSearchResults(SearchCriteria $searchCriteria)
    {
        return $this->campaignStepRepository->getList($searchCriteria);
    }
}
