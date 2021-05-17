<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\Adminhtml\Event\CampaignBase;

use Aitoc\FollowUpEmails\Api\RegisterDataProviderInterface;
use Aitoc\FollowUpEmails\Model\EventManagement;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;

/**
 * Class Edit
 */
abstract class Edit extends Action
{
    const REQUEST_PARAM_EVENT_CODE = 'event_code';

    /**
     * @return int
     */
    abstract protected function getRequestedSubpageId();

    /**
     * @param int $entityId
     * @return string
     */
    abstract protected function getEventCodeByEntityId($entityId);

    /**
     * @return string|null
     */
    abstract protected function getEventCodeByRequestedParentEntityId();

    /**
     * @var Registry
     */
    protected $registerDataProvider;
    /**
     * @var EventManagement
     */
    private $eventManagement;

    /**
     * @param Action\Context $context
     * @param EventManagement $eventManagement
     * @param RegisterDataProviderInterface $registerDataProvider
     */
    public function __construct(
        Action\Context $context,
        EventManagement $eventManagement,
        RegisterDataProviderInterface $registerDataProvider
    ) {
        $this->eventManagement = $eventManagement;
        $this->registerDataProvider = $registerDataProvider;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $this->getByRequestAndSetRegistryCurrentCampaignId();
        $this->getAndSetRegistryCurrentEventCode();

        return $this->createResultPage();
    }

    private function getByRequestAndSetRegistryCurrentCampaignId()
    {
        $campaignId = $this->getRequestedParam('id');
        $this->setRegistryCurrentCampaignId($campaignId);
    }

    /**
     * @param int $campaignId
     */
    private function setRegistryCurrentCampaignId($campaignId)
    {
        $this->registerDataProvider->setCurrentCampaignId($campaignId);
    }

    protected function getAndSetRegistryCurrentEventCode()
    {
        $eventCode = $this->getCurrentEventCode();
        $this->setRegistryCurrentEventCode($eventCode);
    }

    /**
     * @return string
     */
    protected function getCurrentEventCode()
    {
        return ($campaignId = $this->getRequestedSubpageId())
            ? $this->getEventCodeByEntityId($campaignId)
            : $this->getEventCodeByRequestedParentEntityId();
    }

    /**
     * @param string $paramName
     * @return string|null
     */
    protected function getRequestedParam($paramName)
    {
        return $this->getRequest()->getParam($paramName);
    }

    /**
     * @param string $eventCode
     */
    protected function setRegistryCurrentEventCode($eventCode)
    {
        $this->registerDataProvider->setCurrentEventCode($eventCode);
    }

    /**
     * @return ResultInterface
     */
    protected function createResultPage()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
