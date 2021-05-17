<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Ui\Component\Listing\DataProvider;

use Aitoc\FollowUpEmails\Api\Data\EventAttributeInterface;
use Aitoc\FollowUpEmails\Model\Data\Event\CollectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class EventDataProvider
 */
class EventDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request,
     * @param FilterBuilder $filterBuilder,
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection    = $collectionFactory->create();
        $this->request       = $request;
        $this->filterBuilder = $filterBuilder;
        
        $this->prepareUpdateUrl();
    }

    /**
     * @return void
     */
    protected function prepareUpdateUrl()
    {
        if (!isset($this->data['config']['filter_url_params'])) {
            return;
        }
        foreach ($this->data['config']['filter_url_params'] as $paramName => $paramValue) {
            $fieldName = $paramName;
            
            if (is_array($paramValue)) {
                $fieldName  = $paramValue['field'];
                $paramValue = $paramValue['value'];
            }
            
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
                        ->setField($fieldName)
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
        $collection = $this->getCollection();
        $collection->setOrder(EventAttributeInterface::NAME, Collection::SORT_ORDER_ASC);

        if (!$collection->isLoaded()) {
            $collection->load();
        }

        $items = $collection->toArray();

        return $items;
    }
}
