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

use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\Data\EmailSearchResultsInterfaceFactory;
use Aitoc\FollowUpEmails\Api\EmailRepositoryInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailTableInterface;
use Aitoc\FollowUpEmails\Model\EmailFactory as ModelEmailFactory;
use Aitoc\FollowUpEmails\Model\ResourceModel\Email as EmailsResourceModel;
use Aitoc\FollowUpEmails\Model\ResourceModel\Email\CollectionFactory as EmailsCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Config\Dom\ValidationException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class EmailRepository
 */
class EmailRepository implements EmailRepositoryInterface
{
    /**
     * @var EmailsResourceModel
     */
    private $emailsModelResource;

    /**
     * @var ModelEmailFactory
     */
    private $emailModelFactory;

    /**
     * @var EmailsCollectionFactory
     */
    private $emailsCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var EmailSearchResultsInterfaceFactory
     */
    private $emailSearchResultsInterfaceFactory;

    /**
     * @param EmailsResourceModel $emailsModelResource
     * @param ModelEmailFactory $emailsModelFactory
     * @param EmailsCollectionFactory $emailsCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param EmailSearchResultsInterfaceFactory $emailSearchResultsInterfaceFactory
     */
    public function __construct(
        EmailsResourceModel $emailsModelResource,
        ModelEmailFactory $emailsModelFactory,
        EmailsCollectionFactory $emailsCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        EmailSearchResultsInterfaceFactory $emailSearchResultsInterfaceFactory
    ) {
        $this->emailsModelResource = $emailsModelResource;
        $this->emailModelFactory = $emailsModelFactory;
        $this->emailsCollectionFactory = $emailsCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->emailSearchResultsInterfaceFactory = $emailSearchResultsInterfaceFactory;
    }

    /**
     * @param AbstractModel|EmailInterface $emailModel
     * @return EmailInterface
     * @throws CouldNotSaveException
     */
    public function save(EmailInterface $emailModel)
    {
        try {
            $this->emailsModelResource->save($emailModel);
        } catch (ValidationException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save model %1', $emailModel->getEntityId()));
        }

        return $emailModel;
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
     * @return EmailInterface
     * @throws NoSuchEntityException
     */
    public function get($entityId)
    {
        $emailsModel = $this->emailModelFactory->create();
        $this->emailsModelResource->load($emailsModel, $entityId);

        if (!$emailsModel->getEntityId()) {
            throw new NoSuchEntityException(__('Entity with specified ID "%1" not found.', $entityId));
        }

        return $emailsModel;
    }

    /**
     * @param AbstractModel|EmailInterface $emailsModel
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(EmailInterface $emailsModel)
    {
        try {
            $this->emailsModelResource->delete($emailsModel);
        } catch (ValidationException $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to remove entity with ID%', $emailsModel->getEntityId()));
        }

        return true;
    }

    /**
     * @param string $unsubscribeCode
     * @return EmailInterface|AbstractModel|null
     */
    public function getByUnsubscribeCode($unsubscribeCode)
    {
        $emailsModel = $this->createModel();

        $this->emailsModelResource->load($emailsModel, $unsubscribeCode, EmailTableInterface::COLUMN_NAME_SECRET_CODE);

        return $emailsModel->getEntityId() ? $emailsModel : null;
    }

    /**
     * @return EmailInterface|AbstractModel
     */
    private function createModel()
    {
        return $this->emailModelFactory->create();
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return EmailSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->emailsCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->emailSearchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }
}
