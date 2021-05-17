<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper;

use Aitoc\FollowUpEmails\Api\CampaignRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\CampaignStepRepositoryInterface;
use Aitoc\FollowUpEmails\Api\EmailRepositoryInterface;

/**
 * Class Campaign
 */
class Campaign
{
    /**
     * @var CampaignRepositoryInterface
     */
    protected $campaignsRepository;

    /**
     * @var CampaignStepRepositoryInterface
     */
    protected $campaignStepRepository;

    /**
     * @var EmailRepositoryInterface
     */
    protected $emailRepository;

    public function __construct(
        CampaignRepositoryInterface $campaignsRepository,
        CampaignStepRepositoryInterface $campaignStepRepository,
        EmailRepositoryInterface $emailRepository
    ) {
        $this->campaignsRepository = $campaignsRepository;
        $this->campaignStepRepository = $campaignStepRepository;
        $this->emailRepository = $emailRepository;
    }

    /**
     * @param int $campaignId
     * @return string
     */
    public function getEventCodeByCampaignId($campaignId)
    {
        $campaign = $this->getCampaignById($campaignId);

        return $campaign->getEventCode();
    }

    /**
     * @param int $campaignId
     * @return CampaignInterface
     */
    protected function getCampaignById($campaignId)
    {
        return $this->campaignsRepository->get($campaignId);
    }

    /**
     * @param $email
     * @return \Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface
     */
    public function getCampaignStepByEmailId($emailId)
    {
        $email = $this->emailRepository->get($emailId);
        $campaignStepId = $email->getCampaignStepId();

        return $this->getCampaignStepById($campaignStepId);
    }

    /**
     * @param $campaignStepId
     * @return \Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface
     */
    public function getCampaignStepById($campaignStepId)
    {
        return $this->campaignStepRepository->get($campaignStepId);
    }
}
