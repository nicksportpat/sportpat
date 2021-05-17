<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Block\Adminhtml\CampaignCommon\Edit;

use Aitoc\FollowUpEmails\Api\RegisterDataProviderInterface;
use Aitoc\FollowUpEmails\Block\Adminhtml\Campaign\GenericButton;
use Aitoc\FollowUpEmails\Model\Campaign;
use Aitoc\FollowUpEmails\Model\CampaignRepository;
use Aitoc\FollowUpEmails\Model\CampaignStepRepository;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class BackButton
 */
class BackButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var CampaignStepRepository
     */
    protected $campaignStepsRepository;
    /**
     * @var CampaignRepository
     */
    protected $campaignRepository;

    /**
     * @var RegisterDataProviderInterface
     */
    protected $registerDataProvider;

    /**
     * @param Context $context
     * @param CampaignStepRepository $campaignStepsRepository
     * @param CampaignRepository $campaignRepository
     * @param RegisterDataProviderInterface $registerDataProvider
     */
    public function __construct(
        Context $context,
        CampaignStepRepository $campaignStepsRepository,
        CampaignRepository $campaignRepository,
        RegisterDataProviderInterface $registerDataProvider
    ) {
        $this->campaignStepsRepository = $campaignStepsRepository;
        $this->campaignRepository = $campaignRepository;
        $this->registerDataProvider = $registerDataProvider;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $backUrl = $this->getBackUrl();

        return [
            'label' => __('Back'),
            'on_click' => "location.href = '{$backUrl}';",
            'class' => 'back',
            'sort_order' => 10,
        ];
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        $eventCode = $this->getCurrentEventCode();

        return $this->getEventCampaignUrl($eventCode);
    }

    /**
     * @return string
     */
    protected function getCurrentEventCode()
    {
        return $this->registerDataProvider->getCurrentEventCode();
    }

    /**
     * @param string $eventCode
     * @return string
     */
    protected function getEventCampaignUrl($eventCode)
    {
        //rf: use `event_code` as params
        return $this->getUrl('followup/event_campaign/index/event_code/' . $eventCode);
    }

    /**
     * @return null|string
     */
    protected function getRequestedCampaignId()
    {
        $requestedParam = $this->getRequestedParam('campaign_id');

        return $requestedParam ? (int) $requestedParam : $requestedParam;
    }

    /**
     * @param string $paramName
     * @return string|null
     */
    protected function getRequestedParam($paramName)
    {
        return $this->context->getRequest()->getParam($paramName);
    }

    /**
     * @param int $campaignId
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getEventCodeByCampaignId($campaignId)
    {
        $campaign = $this->getCampaignById($campaignId);

        return $campaign->getEventCode();
    }

    /**
     * @param $campaignId
     * @return Campaign
     * @throws NoSuchEntityException
     */
    protected function getCampaignById($campaignId)
    {
        return $this->campaignRepository->get($campaignId);
    }

    /**
     * @return string|null
     */
    protected function getRequestedEventCode()
    {
        return $this->getRequestedParam('event_code');
    }
}
