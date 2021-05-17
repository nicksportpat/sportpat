<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aitoc\FollowUpEmails\Model\EventManagement;
use Aitoc\FollowUpEmails\Model\CampaignRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Aitoc\FollowUpEmails\Model\CampaignStepRepository;

/**
 * Class EventCode
 */
class EventCode extends Column
{
    const EVENT_CODE = 'event_code';
    const CAMPAIGN_STEP_ID = 'campaign_step_id';
    const TEMPLATE_ID = 'template_id';

    /**
     * @var EventManagement
     */
    protected $eventManager;

    /**
     * @var CampaignRepository
     */
    private $campaignsRepository;

    /**
     * @var CampaignStepRepository
     */
    private $campaignStepsRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param EventManagement $eventManager
     * @param CampaignRepository $campaignsRepository
     * @param CampaignStepRepository $campaignStepsRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        EventManagement $eventManager,
        CampaignRepository $campaignsRepository,
        CampaignStepRepository $campaignStepsRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->eventManager = $eventManager;
        $this->campaignsRepository = $campaignsRepository;
        $this->campaignStepsRepository = $campaignStepsRepository;
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[self::CAMPAIGN_STEP_ID])) {
                    $campaignStepModel = $this->campaignStepsRepository->get($item[self::CAMPAIGN_STEP_ID]);
                    $item[self::TEMPLATE_ID] = $campaignStepModel->getTemplateId();
                    $campaignModel = $this->campaignsRepository->get($campaignStepModel->getCampaignId());
                    $item[self::EVENT_CODE] = $this->eventManager->getEventNameByCode($campaignModel->getEventCode());
                }
            }
        }
        return $dataSource;
    }
}
