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

use Aitoc\FollowUpEmails\Api\CampaignStepRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Data;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Data\CampaignStepSearchResultsInterfaceFactory;
use Aitoc\FollowUpEmails\Model\CampaignStepFactory as ModelCampaignStepFactory;
use Aitoc\FollowUpEmails\Model\ResourceModel\CampaignStep as CampaignStepsResourceModel;
use Aitoc\FollowUpEmails\Model\ResourceModel\CampaignStep\CollectionFactory as CampaignStepsCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CampaignStepRepository
 */
class CampaignStepRepository implements CampaignStepRepositoryInterface
{
    /**
     * @var CampaignStepsResourceModel
     */
    private $campaignStepsModelResource;

    /**
     * @var ModelCampaignStepFactory
     */
    private $campaignStepsModelFactory;

    /**
     * @var CampaignStepsCollectionFactory
     */
    private $campaignStepsCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var CampaignStepSearchResultsInterfaceFactory
     */
    private $campaignStepSearchResultsInterfaceFactory;

    /**
     * @param CampaignStepsResourceModel $campaignStepsModelResource
     * @param ModelCampaignStepFactory $campaignStepsModelFactory
     * @param CampaignStepsCollectionFactory $campaignStepsCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CampaignStepSearchResultsInterfaceFactory $campaignStepSearchResultsInterfaceFactory
     */
    public function __construct(
        CampaignStepsResourceModel $campaignStepsModelResource,
        ModelCampaignStepFactory $campaignStepsModelFactory,
        CampaignStepsCollectionFactory $campaignStepsCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        CampaignStepSearchResultsInterfaceFactory $campaignStepSearchResultsInterfaceFactory
    ) {
        $this->campaignStepsModelResource = $campaignStepsModelResource;
        $this->campaignStepsModelFactory = $campaignStepsModelFactory;
        $this->campaignStepsCollectionFactory = $campaignStepsCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->campaignStepSearchResultsInterfaceFactory = $campaignStepSearchResultsInterfaceFactory;
    }

    /**
     * @param CampaignStepInterface|AbstractModel $campaignStepsModel
     * @return CampaignStepInterface
     * @throws CouldNotSaveException
     */
    public function save(CampaignStepInterface $campaignStepsModel)
    {
        try {
            $this->campaignStepsModelResource->save($campaignStepsModel);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__(
                'Unable to save model %1: %1',
                $campaignStepsModel->getEntityId()),
                $e->getMessage()
            );
        }

        return $campaignStepsModel;
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
     * @param int $entityId
     * @return CampaignStep
     * @throws NoSuchEntityException
     */
    public function get($entityId)
    {
        $campaignStepsModel = $this->campaignStepsModelFactory->create();
        $this->campaignStepsModelResource->load($campaignStepsModel, $entityId);

        if (!$campaignStepsModel->getEntityId()) {
            throw new NoSuchEntityException(__('Entity with specified ID "%1" not found.', $entityId));
        }

        return $campaignStepsModel;
    }

    /**
     * @param CampaignStepInterface|AbstractModel $campaignStepsModel
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(CampaignStepInterface $campaignStepsModel)
    {
        try {
            $this->campaignStepsModelResource->delete($campaignStepsModel);
        } catch (ValidationException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to remove entity with ID%', $campaignStepsModel->getEntityId()));
        }

        return true;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return Data\CampaignStepSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->campaignStepsCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->campaignStepSearchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * @param int $campaignId
     * @return CampaignStep
     */
    public function getByCampaignId($campaignId)
    {
        /** @var CampaignStep $model */
        $model = $this->campaignStepsModelFactory->create();
        $this->campaignStepsModelResource->load($model, $campaignId, 'campaign_id');

        return $model;
    }

    /**
     * @param array $ids
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteByIds($ids)
    {
        try {
            $this->campaignStepsModelResource->massDelete($ids);
        } catch (ValidationException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Some emails are invalid'));
        }

        return true;
    }
}
