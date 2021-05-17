<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Model;

use Aitoc\FollowUpEmails\Api\StatisticRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Data;
use Aitoc\FollowUpEmails\Model\ResourceModel\Statistic as StatisticsResourceModel;
use Aitoc\FollowUpEmails\Model\StatisticFactory as ModelStatisticFactory;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\Statistic\CollectionFactory as StatisticsCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Aitoc\FollowUpEmails\Api\Data\StatisticSearchResultsInterfaceFactory;

class StatisticRepository implements StatisticRepositoryInterface
{
    /**
     * @var StatisticsResourceModel
     */
    private $statisticsModelResource;

    /**
     * @var ModelStatisticFactory
     */
    private $statisticsModelFactory;

    /**
     * @var StatisticsCollectionFactory
     */
    private $statisticsCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var StatisticSearchResultsInterfaceFactory
     */
    private $statisticSearchResultsInterfaceFactory;

    /**
     * @param StatisticsResourceModel $statisticsModelResource
     * @param StatisticFactory $statisticsModelFactory
     * @param StatisticsCollectionFactory $statisticsCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param StatisticSearchResultsInterfaceFactory $statisticSearchResultsInterfaceFactory
     */
    public function __construct(
        StatisticsResourceModel $statisticsModelResource,
        ModelStatisticFactory $statisticsModelFactory,
        StatisticsCollectionFactory $statisticsCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        StatisticSearchResultsInterfaceFactory $statisticSearchResultsInterfaceFactory
    ) {
        $this->statisticsModelResource = $statisticsModelResource;
        $this->statisticsModelFactory = $statisticsModelFactory;
        $this->statisticsCollectionFactory = $statisticsCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->statisticSearchResultsInterfaceFactory = $statisticSearchResultsInterfaceFactory;
    }

    /**
     * @param Data\StatisticInterface $statisticsModel
     * @return Data\StatisticInterface
     * @throws CouldNotSaveException
     */
    public function save(Data\StatisticInterface $statisticsModel)
    {
        try {
            $this->statisticsModelResource->save($statisticsModel);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save model %1', $statisticsModel->getEntityId()));
        }

        return $statisticsModel;
    }

    /**
     * @param int $entityId
     * @return Statistic
     * @throws NoSuchEntityException
     */
    public function get($entityId)
    {
        $statisticsModel = $this->statisticsModelFactory->create();
        $this->statisticsModelResource->load($statisticsModel, $entityId);

        if (!$statisticsModel->getEntityId()) {
            throw new NoSuchEntityException(__('Entity with specified ID "%1" not found.', $entityId));
        }

        return $statisticsModel;
    }

    /**
     * @param Data\StatisticInterface $statisticsModel
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\StatisticInterface $statisticsModel)
    {
        try {
            $this->statisticsModelResource->delete($statisticsModel);
        } catch (ValidationException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to remove entity with ID%', $statisticsModel->getEntityId()));
        }

        return true;
    }

    /**
     * @param int $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($entityId)
    {
        $model = $this->get($entityId);
        $this->delete($model);

        return true;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return Data\StatisticSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->statisticsCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->statisticSearchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }
}
