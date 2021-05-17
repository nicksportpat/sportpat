<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\Adminhtml\Email;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aitoc\FollowUpEmails\Model\EmailRepository;
use Aitoc\FollowUpEmails\Model\ResourceModel\Email\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class MassDelete extends Action
{
    /**
     * @var EmailRepository
     */
    private $emailsRepository;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var $filter
     */
    private $filter;

    /**
     * @param Context $context
     * @param EmailRepository $emailRepository
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        EmailRepository $emailRepository,
        CollectionFactory $collectionFactory,
        Filter $filter
    ) {
        $this->emailsRepository = $emailRepository;
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('followup/email/index');
        try {
            $collectionSize = $collection->getSize();
            foreach ($collection as $item) {
                $this->emailsRepository->deleteById($item->getId());
            }
            $this->messageManager->addSuccessMessage(__('%1 email(s) were successfully deleted.', $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $redirectResult;
    }
}
