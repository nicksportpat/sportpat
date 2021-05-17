<?php
/**
 * A Magento 2 module named Ncloutier/Shippingsentence
 * Copyright (C) 2017  
 * 
 * This file is part of Ncloutier/Shippingsentence.
 * 
 * Ncloutier/Shippingsentence is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Ncloutier\Shippingsentence\Model;

use Ncloutier\Shippingsentence\Api\SentencesRepositoryInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Ncloutier\Shippingsentence\Api\Data\SentencesSearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Ncloutier\Shippingsentence\Model\ResourceModel\Sentences\CollectionFactory as SentencesCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Ncloutier\Shippingsentence\Model\ResourceModel\Sentences as ResourceSentences;
use Magento\Framework\Exception\CouldNotDeleteException;
use Ncloutier\Shippingsentence\Api\Data\SentencesInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\StoreManagerInterface;

class SentencesRepository implements SentencesRepositoryInterface
{

    protected $resource;

    protected $sentencesCollectionFactory;

    protected $dataObjectHelper;

    private $storeManager;

    protected $dataSentencesFactory;

    protected $sentencesFactory;

    protected $searchResultsFactory;

    protected $dataObjectProcessor;


    /**
     * @param ResourceSentences $resource
     * @param SentencesFactory $sentencesFactory
     * @param SentencesInterfaceFactory $dataSentencesFactory
     * @param SentencesCollectionFactory $sentencesCollectionFactory
     * @param SentencesSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceSentences $resource,
        SentencesFactory $sentencesFactory,
        SentencesInterfaceFactory $dataSentencesFactory,
        SentencesCollectionFactory $sentencesCollectionFactory,
        SentencesSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->sentencesFactory = $sentencesFactory;
        $this->sentencesCollectionFactory = $sentencesCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataSentencesFactory = $dataSentencesFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Ncloutier\Shippingsentence\Api\Data\SentencesInterface $sentences
    ) {
        /* if (empty($sentences->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $sentences->setStoreId($storeId);
        } */
        try {
            $this->resource->save($sentences);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the sentences: %1',
                $exception->getMessage()
            ));
        }
        return $sentences;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($sentencesId)
    {
        $sentences = $this->sentencesFactory->create();
        $this->resource->load($sentences, $sentencesId);
        if (!$sentences->getId()) {
            throw new NoSuchEntityException(__('Sentences with id "%1" does not exist.', $sentencesId));
        }
        return $sentences;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->sentencesCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Ncloutier\Shippingsentence\Api\Data\SentencesInterface $sentences
    ) {
        try {
            $this->resource->delete($sentences);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Sentences: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($sentencesId)
    {
        return $this->delete($this->getById($sentencesId));
    }
}
