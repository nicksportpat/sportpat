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

use Aitoc\FollowUpEmails\Api\CampaignRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignSearchResultsInterfaceFactory;
use Aitoc\FollowUpEmails\Model\CampaignFactory as ModelCampaignFactory;
use Aitoc\FollowUpEmails\Model\ResourceModel\Campaign as CampaignsResourceModel;
use Aitoc\FollowUpEmails\Model\ResourceModel\Campaign\CollectionFactory as CampaignsCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CampaignRepository
 */
class CampaignRepository implements CampaignRepositoryInterface
{
    /**
     * @var CampaignsResourceModel
     */
    private $campaignsModelResource;

    /**
     * @var ModelCampaignFactory
     */
    private $campaignsModelFactory;

    /**
     * @var CampaignsCollectionFactory
     */
    private $campaignsCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var CampaignSearchResultsInterfaceFactory
     */
    private $campaignSearchResultsInterfaceFactory;

    /**
     * @param CampaignsResourceModel $campaignsModelResource
     * @param ModelCampaignFactory $campaignsModelFactory
     * @param CampaignsCollectionFactory $campaignsCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CampaignSearchResultsInterfaceFactory $campaignSearchResultsInterfaceFactory
     */
    public function __construct(
        CampaignsResourceModel $campaignsModelResource,
        ModelCampaignFactory $campaignsModelFactory,
        CampaignsCollectionFactory $campaignsCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        CampaignSearchResultsInterfaceFactory $campaignSearchResultsInterfaceFactory
    ) {
        $this->campaignsModelResource = $campaignsModelResource;
        $this->campaignsModelFactory = $campaignsModelFactory;
        $this->campaignsCollectionFactory = $campaignsCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->campaignSearchResultsInterfaceFactory = $campaignSearchResultsInterfaceFactory;
    }

    /**
     * @param CampaignInterface|AbstractModel $campaignsModel
     * @return CampaignInterface
     * @throws CouldNotSaveException
     */
    public function save(CampaignInterface $campaignsModel)
    {
        try {
            $this->campaignsModelResource->save($campaignsModel);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save model %1', $campaignsModel->getEntityId()));
        }

        return $campaignsModel;
    }

    /**
     * @param int $entityId
     * @return Campaign
     * @throws NoSuchEntityException
     */
    public function get($entityId)
    {
        /** @var Campaign $campaignsModel */
        $campaignsModel = $this->createModel();
        $this->loadToModel($campaignsModel, $entityId);

        if ($campaignsModel->isEmpty()) {
            throw new NoSuchEntityException(__('Entity with specified ID "%1" not found.', $entityId));
        }

        return $campaignsModel;
    }

    /**
     * @return CampaignInterface
     */
    protected function createModel()
    {
        return $this->campaignsModelFactory->create();
    }

    /**
     * @param CampaignInterface|AbstractModel $campaignsModel
     * @param int $entityId
     */
    protected function loadToModel($campaignsModel, $entityId)
    {
        $this->campaignsModelResource->load($campaignsModel, $entityId);
    }

    /**
     * @param CampaignInterface|AbstractModel $campaignsModel
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(CampaignInterface $campaignsModel)
    {
        try {
            $this->campaignsModelResource->delete($campaignsModel);
        } catch (ValidationException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to remove entity with ID%', $campaignsModel->getEntityId()));
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
     * @return CampaignSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->campaignsCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->campaignSearchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }
}
