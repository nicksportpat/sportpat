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

use Aitoc\FollowUpEmails\Api\Data\EmailAttributeInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailAttributeSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailAttributeSearchResultsInterfaceFactory;
use Aitoc\FollowUpEmails\Api\EmailAttributeRepositoryInterface;
use Aitoc\FollowUpEmails\Model\EmailAttributeFactory as ModelEmailAttributeFactory;
use Aitoc\FollowUpEmails\Model\ResourceModel\EmailAttribute as EmailAttributesResourceModel;
use Aitoc\FollowUpEmails\Model\ResourceModel\EmailAttribute\CollectionFactory as EmailAttributesCollectionFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Aitoc\FollowUpEmails\Helper\SearchCriteriaBuilder as SearchCriteriaBuilderHelper;

/**
 * Class EmailAttributeRepository
 */
class EmailAttributeRepository implements EmailAttributeRepositoryInterface
{
    /**
     * @var EmailAttributesResourceModel
     */
    private $emailAttributesModelResource;

    /**
     * @var ModelEmailAttributeFactory
     */
    private $emailAttributesModelFactory;

    /**
     * @var EmailAttributesCollectionFactory
     */
    private $emailAttributesCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var EmailAttributeSearchResultsInterfaceFactory
     */
    private $emailAttributeSearchResultsInterfaceFactory;

    /**
     * @var SearchCriteriaBuilderHelper
     */
    private $searchCriteriaBuilderHelper;

    /**
     * @param EmailAttributesResourceModel $emailAttributesModelResource
     * @param ModelEmailAttributeFactory $emailAttributesModelFactory
     * @param EmailAttributesCollectionFactory $emailAttributesCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param EmailAttributeSearchResultsInterfaceFactory $emailAttributeSearchResultsInterfaceFactory
     * @param SearchCriteriaBuilderHelper $searchCriteriaBuilderHelper
     */
    public function __construct(
        EmailAttributesResourceModel $emailAttributesModelResource,
        ModelEmailAttributeFactory $emailAttributesModelFactory,
        EmailAttributesCollectionFactory $emailAttributesCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        EmailAttributeSearchResultsInterfaceFactory $emailAttributeSearchResultsInterfaceFactory,
        SearchCriteriaBuilderHelper $searchCriteriaBuilderHelper
    ) {
        $this->emailAttributesModelResource = $emailAttributesModelResource;
        $this->emailAttributesModelFactory = $emailAttributesModelFactory;
        $this->emailAttributesCollectionFactory = $emailAttributesCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->emailAttributeSearchResultsInterfaceFactory = $emailAttributeSearchResultsInterfaceFactory;
        $this->searchCriteriaBuilderHelper = $searchCriteriaBuilderHelper;
    }

    /**
     * @param EmailAttributeInterface|AbstractModel $emailAttributesModel
     * @return EmailAttributeInterface
     * @throws CouldNotSaveException
     */
    public function save(EmailAttributeInterface $emailAttributesModel)
    {
        try {
            $this->emailAttributesModelResource->save($emailAttributesModel);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save model %1', $emailAttributesModel->getEntityId()));
        }

        return $emailAttributesModel;
    }

    /**
     * @param int $entityId
     * @return EmailAttribute
     * @throws NoSuchEntityException
     */
    public function get($entityId)
    {
        $emailAttributesModel = $this->emailAttributesModelFactory->create();
        $this->emailAttributesModelResource->load($emailAttributesModel, $entityId);

        if (!$emailAttributesModel->getEntityId()) {
            throw new NoSuchEntityException(__('Entity with specified ID "%1" not found.', $entityId));
        }

        return $emailAttributesModel;
    }

    /**
     * @param EmailAttributeInterface|AbstractModel $emailAttributesModel
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(EmailAttributeInterface $emailAttributesModel)
    {
        try {
            $this->emailAttributesModelResource->delete($emailAttributesModel);
        } catch (ValidationException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to remove entity with ID%', $emailAttributesModel->getEntityId()));
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
     * @return EmailAttributeSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->emailAttributesCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->emailAttributeSearchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @param array $filters
     * @return EmailAttributeInterface[]
     */
    public function getByFilters($filters)
    {
        $searchCriteria = $this->createSearchCriteria($filters);
        $searchResults = $this->getList($searchCriteria);

        return $searchResults->getItems();
    }

    /**
     * @param $filters
     * @return SearchCriteria
     */
    private function createSearchCriteria($filters)
    {
        return $this->searchCriteriaBuilderHelper->createSearchCriteria($filters);
    }
}
