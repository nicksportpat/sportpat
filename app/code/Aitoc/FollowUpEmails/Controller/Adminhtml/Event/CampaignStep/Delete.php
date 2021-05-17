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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aitoc\FollowUpEmails\Model\CampaignStepRepository;
use Aitoc\FollowUpEmails\Model\CampaignRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\Redirect;

class Delete extends Action
{
    /**
     * @var CampaignStepRepository
     */
    private $campaignStepsRepository;

    /**
     * @var CampaignRepository
     */
    private $campaignRepository;

    /**
     * @param Context $context
     * @param CampaignStepRepository $campaignStepsRepository
     * @param CampaignRepository $campaignRepository
     */
    public function __construct(
        Context $context,
        CampaignStepRepository $campaignStepsRepository,
        CampaignRepository $campaignRepository
    ) {
        $this->campaignStepsRepository = $campaignStepsRepository;
        $this->campaignRepository = $campaignRepository;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectRoute = 'followup/event_campaign/index';
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $campaignStep = $this->campaignStepsRepository->get($id);
                if ($campaignStep->getId()) {
                    $campaign = $this->campaignRepository->get($campaignStep->getCampaignId());
                    $redirectRoute = 'followup/event_campaign/index/event_code/' . $campaign->getEventCode();
                }
                $this->campaignStepsRepository->delete($campaignStep);
                $this->messageManager->addSuccessMessage(__('Email was successfully deleted'));
                $redirectResult->setPath($redirectRoute);
                return $redirectResult;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        $redirectResult->setPath($redirectRoute);
        return $redirectResult;
    }
}
