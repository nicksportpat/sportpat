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

use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressInterface;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressInterfaceFactory;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressSearchResultsInterfaceFactory;
use Aitoc\FollowUpEmails\Api\Setup\Current\UnsubscribedEmailAddressTableInterface;
use Aitoc\FollowUpEmails\Api\UnsubscribedEmailAddressRepositoryInterface;
use Aitoc\FollowUpEmails\Model\ResourceModel\UnsubscribedEmailAddress as UnsubscribedListResourceModel;
use Aitoc\FollowUpEmails\Model\ResourceModel\UnsubscribedEmailAddress\Collection;
use Aitoc\FollowUpEmails\Model\ResourceModel\UnsubscribedEmailAddress\CollectionFactory as UnsubscribedListCollectionFactory;
use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class UnsubscribedEmailAddressRepository
 */
class UnsubscribedEmailAddressRepository implements UnsubscribedEmailAddressRepositoryInterface
{
    /**
     * @var UnsubscribedListResourceModel
     */
    private $unsubscribedListModelResource;

    /**
     * @var UnsubscribedEmailAddressInterfaceFactory
     */
    private $unsubscribedEmailAddressFactory;

    /**
     * @var UnsubscribedListCollectionFactory
     */
    private $unsubscribeEmailAddressCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var UnsubscribedEmailAddressSearchResultsInterfaceFactory
     */
    private $unsubscribedEmailAddressSearchResultsFactory;

    /**
     * @param UnsubscribedListResourceModel $unsubscribedListModelResource
     * @param UnsubscribedEmailAddressInterfaceFactory $unsubscribedEmailAddressFactory
     * @param UnsubscribedListCollectionFactory $unsubscribedListCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param UnsubscribedEmailAddressSearchResultsInterfaceFactory $unsubscribedEmailAddressSearchResultsFactory
     */
    public function __construct(
        UnsubscribedListResourceModel $unsubscribedListModelResource,
        UnsubscribedEmailAddressInterfaceFactory $unsubscribedEmailAddressFactory,
        UnsubscribedListCollectionFactory $unsubscribedListCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        UnsubscribedEmailAddressSearchResultsInterfaceFactory $unsubscribedEmailAddressSearchResultsFactory
    ) {
        $this->unsubscribedListModelResource = $unsubscribedListModelResource;
        $this->unsubscribedEmailAddressFactory = $unsubscribedEmailAddressFactory;
        $this->unsubscribeEmailAddressCollectionFactory = $unsubscribedListCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->unsubscribedEmailAddressSearchResultsFactory = $unsubscribedEmailAddressSearchResultsFactory;
    }

    /**
     * @param UnsubscribedEmailAddressInterface|AbstractModel $unsubscribedListModel
     * @return UnsubscribedEmailAddressInterface
     * @throws CouldNotSaveException
     */
    public function save(UnsubscribedEmailAddressInterface $unsubscribedListModel)
    {
        try {
            $this->unsubscribedListModelResource->save($unsubscribedListModel);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Unable to save model %1', $unsubscribedListModel->getEntityId()));
        }

        return $unsubscribedListModel;
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
     * @return UnsubscribedEmailAddressInterface
     * @throws NoSuchEntityException
     */
    public function get($entityId)
    {
        $unsubscribedEmailAddressModel = $this->create();
        $this->unsubscribedListModelResource->load($unsubscribedEmailAddressModel, $entityId);

        if (!$unsubscribedEmailAddressModel->getEntityId()) {
            throw new NoSuchEntityException(__('Entity with specified ID "%1" not found.', $entityId));
        }

        return $unsubscribedEmailAddressModel;
    }

    /**
     * @return UnsubscribedEmailAddressInterface
     */
    public function create()
    {
        return $this->unsubscribedEmailAddressFactory->create();
    }

    /**
     * @param UnsubscribedEmailAddressInterface|AbstractModel $unsubscribedListModel
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(UnsubscribedEmailAddressInterface $unsubscribedListModel)
    {
        try {
            $this->unsubscribedListModelResource->delete($unsubscribedListModel);
        } catch (ValidationException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        } catch (Exception $e) {
            throw new CouldNotDeleteException(__('Unable to remove entity with ID%', $unsubscribedListModel->getEntityId()));
        }

        return true;
    }

    /**
     * @param string $emailAddress
     * @return UnsubscribedEmailAddressInterface[]|DataObject[]
     */
    public function getByEmailAddress($emailAddress)
    {
        $collection = $this->createCollection();
        $collection->addFilter(UnsubscribedEmailAddressTableInterface::COLUMN_NAME_CUSTOMER_EMAIL, $emailAddress);

        return $collection->getItems();
    }

    /**
     * @return Collection
     */
    private function createCollection()
    {
        return $this->unsubscribeEmailAddressCollectionFactory->create();;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return UnsubscribedEmailAddressSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->createCollection();
        $this->processCollection($collection, $searchCriteria);
        $searchResults = $this->createSearchResults();
        $items = $collection->getItems();
        $count = count($items);

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($items);
        $searchResults->setTotalCount($count);

        return $searchResults;
    }

    /**
     * @param Collection $collection
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function processCollection(Collection $collection, SearchCriteriaInterface $searchCriteria)
    {
        $this->collectionProcessor->process($searchCriteria, $collection);
    }

    /**
     * @return UnsubscribedEmailAddressSearchResultsInterface
     */
    private function createSearchResults()
    {
        return $this->unsubscribedEmailAddressSearchResultsFactory->create();
    }
}
