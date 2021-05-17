<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Ui\Component\Listing\DataProvider\Event;

use Aitoc\FollowUpEmails\Api\Setup\Current\CampaignStepTableInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\Campaign\CollectionFactory;
use Aitoc\FollowUpEmails\Model\ResourceModel\CampaignStep\CollectionFactory as StepsCollectionFactory;
use Aitoc\FollowUpEmails\Model\StatisticManagement;
use Magento\Email\Model\ResourceModel\Template\CollectionFactory as TemplateCollectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class CampaignDataProvider
 */
class CampaignDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StepsCollectionFactory
     */
    private $stepsCollectionFactory;
    
    /**
     * @var TemplateCollectionFactory
     */
    private $templateCollectionFactory;
    
    /**
     * @var StatisticManagement
     */
    private $statisticManager;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param StepsCollectionFactory $stepsCollectionFactory
     * @param TemplateCollectionFactory $templateCollectionFactory,
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param StatisticManagement $statisticManager;
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CollectionFactory $collectionFactory,
        TemplateCollectionFactory $templateCollectionFactory,
        StepsCollectionFactory $stepsCollectionFactory,
        StatisticManagement $statisticManager,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->stepsCollectionFactory = $stepsCollectionFactory;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->request = $request;
        $this->filterBuilder = $filterBuilder;
        $this->statisticManager = $statisticManager;
        
        $this->prepareUpdateUrl();
    }

    /**
     * @return void
     */
    private function prepareUpdateUrl()
    {
        if (!isset($this->data['config']['filter_url_params'])) {
            return;
        }
        foreach ($this->data['config']['filter_url_params'] as $paramName => $paramValue) {
            if ('*' == $paramValue) {
                $paramValue = $this->request->getParam($paramName);
            }
            if ($paramValue) {
                $this->data['config']['update_url'] = sprintf(
                    '%s%s/%s/',
                    $this->data['config']['update_url'],
                    $paramName,
                    $paramValue
                );
                $this->addFilter(
                    $this->filterBuilder
                        ->setField($paramName)
                        ->setValue($paramValue)
                        ->setConditionType('eq')
                        ->create()
                );
            }
        }
    }
    
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        
        $templates = $this->getTemplates();
        $items     = $this->getCollection()->toArray();
        
        foreach ($items['items'] as & $item) {
            $steps = $this->stepsCollectionFactory->create();
            $steps->addFieldToFilter(
                CampaignStepTableInterface::COLUMN_NAME_CAMPAIGN_ID,
                [
                    'eq' => $item[CampaignStepTableInterface::COLUMN_NAME_ENTITY_ID]
                ]
            )
                ->load();
            
            $stepsData = $steps->getData();
            
            foreach ($stepsData as & $step) {
                $step['template']  = $templates[$step[CampaignStepTableInterface::COLUMN_NAME_TEMPLATE_ID]] ?? '';
                
                $step['statistic'] = $this->statisticManager
                    ->getStatisticByCampaignStep($step[CampaignStepTableInterface::COLUMN_NAME_ENTITY_ID]);
            }
            
            $item['emails'] = $stepsData;
        }
        
        return $items;
    }
    
    /**
     * Get Templates
     *
     * @return array
     */
    private function getTemplates()
    {
        $templates = $this->templateCollectionFactory->create()->getData();
        
        if (empty($templates)) {
            return [];
        }
        
        return array_combine(
            array_column($templates, 'template_id'),
            array_column($templates, 'template_code')
        );
    }
}
