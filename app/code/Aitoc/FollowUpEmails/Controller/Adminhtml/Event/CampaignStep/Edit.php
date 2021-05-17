<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Controller\Adminhtml\Event\CampaignStep;

use Aitoc\FollowUpEmails\Api\CampaignRepositoryInterface;
use Aitoc\FollowUpEmails\Api\CampaignStepRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\RegisterDataProviderInterface;
use Aitoc\FollowUpEmails\Controller\Adminhtml\Event\CampaignBase\Edit as BaseEdit;
use Aitoc\FollowUpEmails\Model\EventManagement;
use Magento\Backend\App\Action;

/**
 * Class Edit
 */
class Edit extends BaseEdit
{
    const REQUEST_PARAM_CAMPAIGN_STEP_ID = 'id';
    const REQUEST_PARAM_CAMPAIGN_ID = 'campaign_id';

    /**
     * @var CampaignStepRepositoryInterface
     */
    protected $campaignStepsRepository;

    /**
     * @var CampaignRepositoryInterface
     */
    protected $campaignsRepository;

    public function __construct(
        Action\Context $context,
        EventManagement $eventManagement,
        RegisterDataProviderInterface $registerDataProvider,
        CampaignStepRepositoryInterface $campaignStepRepository,
        CampaignRepositoryInterface $campaignsRepository
    ) {
        parent::__construct($context, $eventManagement, $registerDataProvider);

        $this->campaignStepsRepository = $campaignStepRepository;
        $this->campaignsRepository = $campaignsRepository;
    }

    protected function getEventCodeByRequestedParentEntityId()
    {
        $campaignId = $this->getRequestedCampaignId();
        $campaign = $this->getCampaignById($campaignId);

        return $campaign->getEventCode();
    }

    /**
     * @return string|null
     */
    protected function getRequestedCampaignId()
    {
        return $this->getRequestedParam(self::REQUEST_PARAM_CAMPAIGN_ID);
    }

    /**
     * @param int $campaignId
     * @return CampaignInterface
     */
    protected function getCampaignById($campaignId)
    {
        return $this->campaignsRepository->get($campaignId);
    }

    protected function getRequestedSubpageId()
    {
        return $this->getRequestedCampaignStepId();
    }

    /**
     * @return int
     */
    protected function getRequestedCampaignStepId()
    {
        return (int) $this->getRequestedParam(self::REQUEST_PARAM_CAMPAIGN_STEP_ID);
    }

    /**
     * @param int $entityId
     * @return string
     */
    protected function getEventCodeByEntityId($entityId)
    {
        return $this->getEventCodeByCampaignStepId($entityId);
    }

    /**
     * @param int $campaignId
     * @return string
     */
    protected function getEventCodeByCampaignStepId($campaignId)
    {
        $campaignStep = $this->getCampaignStepById($campaignId);
        $campaignId = $campaignStep->getCampaignId();
        $campaign = $this->getCampaignById($campaignId);

        return $campaign->getEventCode();
    }

    /**
     * @param int $campaignStepId
     * @return CampaignStepInterface
     */
    protected function getCampaignStepById($campaignStepId)
    {
        return $this->campaignStepsRepository->get($campaignStepId);
    }
}
