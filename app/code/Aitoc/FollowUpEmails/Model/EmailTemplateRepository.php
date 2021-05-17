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

use Aitoc\FollowUpEmails\Api\Data\EmailTemplateSearchResultsInterfaceFactory;
use Aitoc\FollowUpEmails\Api\EmailTemplateRepositoryInterface;
use Magento\Email\Model\ResourceModel\Template as EmailTemplateResource;
use Magento\Email\Model\ResourceModel\Template\CollectionFactory as EmailTemplateCollectionFactory;
use Magento\Email\Model\Template;
use Magento\Email\Model\TemplateFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Mail\TemplateInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class EmailTemplateRepository
 */
class EmailTemplateRepository implements EmailTemplateRepositoryInterface
{
    /**
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * @var EmailTemplateResource
     */
    private $templateResource;

    /**
     * @var EmailTemplateCollectionFactory
     */
    private $templateCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var EmailTemplateSearchResultsInterfaceFactory
     */
    private $templateSearchResultsFactory;

    /**
     * EmailTemplateRepository constructor.
     * @param TemplateFactory $templateFactory
     * @param EmailTemplateResource $templateResource
     * @param EmailTemplateCollectionFactory $templateCollectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param EmailTemplateSearchResultsInterfaceFactory $emailTemplateSearchResultsFactory
     */
    public function __construct(
        TemplateFactory $templateFactory,
        EmailTemplateResource $templateResource,
        EmailTemplateCollectionFactory $templateCollectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        EmailTemplateSearchResultsInterfaceFactory $emailTemplateSearchResultsFactory
    ) {
        $this->templateFactory = $templateFactory;
        $this->templateResource = $templateResource;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->templateSearchResultsFactory = $emailTemplateSearchResultsFactory;
    }

    /**
     * @inheritdoc
     */
    public function get($emailTemplateId)
    {
        $templateEmailModel = $this->createEmailTemplate();
        $this->templateResource->load($templateEmailModel, $emailTemplateId);

        return !$templateEmailModel->isEmpty() ? $templateEmailModel : null;
    }

    /**
     * @return Template
     */
    protected function createEmailTemplate()
    {
        return $this->templateFactory->create();
    }

    /**
     * @param TemplateInterface|AbstractModel $emailTemplate
     * @return TemplateInterface
     * @throws AlreadyExistsException
     */
    public function save(TemplateInterface $emailTemplate)
    {
        $this->templateResource->save($emailTemplate);

        return $emailTemplate;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteria $searchCriteria)
    {
        $collection = $this->templateCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->templateSearchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }
}
