<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Controller\Adminhtml\Event\Campaign;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aitoc\FollowUpEmails\Model\CampaignRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\Redirect;

class DeleteCampaign extends Action
{
    /**
     * @var CampaignRepository
     */
    protected $campaignsRepository;

    /**
     * @param Context $context
     * @param CampaignRepository $campaignRepository
     */
    public function __construct(
        Context $context,
        CampaignRepository $campaignRepository
    ) {
        $this->campaignsRepository = $campaignRepository;
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
                $campaign = $this->campaignsRepository->get($id);
                if ($campaign->getId()) {
                    $redirectRoute = 'followup/event_campaign/index/event_code/' . $campaign->getEventCode();
                }
                $this->campaignsRepository->delete($campaign);
                $this->messageManager->addSuccessMessage(__('Campaign was successfully deleted'));
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
