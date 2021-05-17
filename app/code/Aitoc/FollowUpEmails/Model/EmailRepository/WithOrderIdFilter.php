<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Model\EmailRepository;

use Aitoc\FollowUpEmails\Api\Data\EmailSearchResultsInterfaceFactory;
use Aitoc\FollowUpEmails\Api\EmailRepository\WithOrderIdFilterInterface as WithOrderIdFilterInterfaceEmailRepositoryInterface;
use Aitoc\FollowUpEmails\Model\EmailFactory as ModelEmailFactory;
use Aitoc\FollowUpEmails\Model\EmailRepository as FueEmailRepository;
use Aitoc\FollowUpEmails\Model\ResourceModel\Email as EmailsResourceModel;
use Aitoc\FollowUpEmails\Model\ResourceModel\Email\Collection\Processor as EmailCollectionProcessor;
use Aitoc\FollowUpEmails\Model\ResourceModel\Email\CollectionFactory as EmailsCollectionFactory;

/**
 * Class EmailRepository
 */
class WithOrderIdFilter extends FueEmailRepository implements WithOrderIdFilterInterfaceEmailRepositoryInterface
{
    /**
     * EmailRepository constructor.
     * @param EmailsResourceModel $emailsModelResource
     * @param ModelEmailFactory $emailsModelFactory
     * @param EmailsCollectionFactory $emailsCollectionFactory
     * @param EmailCollectionProcessor $collectionProcessor
     * @param EmailSearchResultsInterfaceFactory $emailSearchResultsInterfaceFactory
     */
    public function __construct(
        EmailsResourceModel $emailsModelResource,
        ModelEmailFactory $emailsModelFactory,
        EmailsCollectionFactory $emailsCollectionFactory,
        EmailCollectionProcessor $collectionProcessor,
        EmailSearchResultsInterfaceFactory $emailSearchResultsInterfaceFactory
    ) {
        parent::__construct(
            $emailsModelResource,
            $emailsModelFactory,
            $emailsCollectionFactory,
            $collectionProcessor,
            $emailSearchResultsInterfaceFactory
        );
    }
}
