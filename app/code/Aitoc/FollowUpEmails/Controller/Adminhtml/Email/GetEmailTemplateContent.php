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
use Magento\Email\Model\Template;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class GetEmailTemplateContent
 */
class GetEmailTemplateContent extends Action
{
    const REQUEST_PARAM_NAME_TEMPLATE_ID = 'template_id';

    const RESPONSE_KEY_TEMPLATE_SUBJECT = 'templateSubject';
    const RESPONSE_KEY_TEMPLATE_CONTENT = 'templateContent';

    /**
     * @var Template
     */
    private $emailTemplateModel;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @param Context $context
     * @param Template $emailTemplateModel
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Template $emailTemplateModel,
        JsonFactory $resultJsonFactory
    )
    {
        $this->emailTemplateModel = $emailTemplateModel;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface|void
     */
    public function execute()
    {
        /** @var HttpRequest $request */
        $request = $this->getRequest();
        $templateId = $this->getRequestedTemplateId($request);

        if (!$request->isAjax() || !$templateId) {
            return;
        }

        $emailTemplate = $this->getTemplateById($templateId);

        $templateSubject = $emailTemplate->getTemplateSubject();
        $templateContent = $emailTemplate->getTemplateText();

        $resultJson = $this->createResultJson();

        return $resultJson->setData([
            self::RESPONSE_KEY_TEMPLATE_SUBJECT => $templateSubject,
            self::RESPONSE_KEY_TEMPLATE_CONTENT => $templateContent,
        ]);
    }

    /**
     * @param RequestInterface $request
     * @return int
     */
    private function getRequestedTemplateId(RequestInterface $request)
    {
        return $request->getParam(self::REQUEST_PARAM_NAME_TEMPLATE_ID);
    }

    /**
     * @param int $templateId
     * @return Template
     */
    private function getTemplateById($templateId)
    {
        // method load is deprecated but there is no repository to get this model.
        return $this->emailTemplateModel->load($templateId);
    }

    /**
     * @return Json
     */
    private function createResultJson()
    {
        return $this->resultJsonFactory->create();
    }
}
