<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Controller\Adminhtml\Email;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Aitoc\FollowUpEmails\Model\CampaignRepository;
use Magento\Framework\App\Area;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Email\Model\TemplateFactory;

class AjaxTrialEmail extends Action
{
    const TEST_EMAIL_ID = 'follow_up_test_email';
    const CAMPAIGN_ID = 'campaign_id';
    const CONTENT = 'content';
    const EMAIL = 'email';
    const CONTENT_VAR = 'email_content';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var CampaignRepository
     */
    private $campaignsRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        TransportBuilder $transportBuilder,
        CampaignRepository $campaignsRepository,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        TemplateFactory $templateFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->transportBuilder = $transportBuilder;
        $this->campaignsRepository = $campaignsRepository;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->templateFactory = $templateFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $campaignId = $this->getRequest()->getParam(self::CAMPAIGN_ID);
        $content = $this->getRequest()->getParam(self::CONTENT);
        $sendTo = $this->getRequest()->getParam(self::EMAIL);

        $campaign = $this->campaignsRepository->get($campaignId);
        $emailSenderContact = $campaign->getSender();
        $storeId = $this->storeManager->getStore()->getId();
        $emailTemplate = $this->templateFactory->create();
        $emailTemplate->setDesignConfig(
            [
                'area' => 'frontend',
                'store' => $storeId
            ]
        );

        $emailTemplate->setTemplateText($content);
        $content = $emailTemplate->getProcessedTemplate();

        $templateVars = [self::CONTENT_VAR => $content];

        $transport = $this->transportBuilder->setTemplateIdentifier(
            self::TEST_EMAIL_ID
        )->setTemplateOptions(
            ['area' => Area::AREA_ADMINHTML, 'store' => $storeId]
        )->setFrom(
            $emailSenderContact
        )->setTemplateVars(
            $templateVars
        )->addTo(
            $sendTo
        )->getTransport();
        try {
            $transport->sendMessage();
        } catch (MailException $e) {
            $this->logger->critical($e);
        }

        $resultJsonFactory = $this->resultJsonFactory->create();
        return $resultJsonFactory->setData(['is_error' => false]);
    }
}
