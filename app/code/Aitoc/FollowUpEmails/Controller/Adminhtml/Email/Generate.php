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

use Aitoc\FollowUpEmails\Model\EmailFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Aitoc\FollowUpEmails\Service\EmailManagement;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Generate extends Action
{
    /**
     * @var EmailFactory
     */
    private $emailFactory;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var EmailManagement
     */
    private $emailManagement;

    /**
     * @param Action\Context $context
     * @param EmailFactory $emailFactory
     * @param PageFactory $resultPageFactory
     * @param EmailManagement $emailManagement
     */
    public function __construct(
        Action\Context $context,
        EmailFactory $emailFactory,
        PageFactory $resultPageFactory,
        EmailManagement $emailManagement
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->emailFactory = $emailFactory;
        $this->emailManagement = $emailManagement;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $redirectResult = $this->resultRedirectFactory->create();
        $this->emailManagement->generateEmails();
        $this->messageManager->addSuccessMessage('Reminders successfully generated.');
        $redirectResult->setPath('followup/*/');

        return $redirectResult;
    }
}
