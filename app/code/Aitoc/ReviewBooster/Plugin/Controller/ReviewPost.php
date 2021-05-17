<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Plugin\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Review\Model\Review;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\ResultFactory;

class ReviewPost
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var ResultFactory
     */
    protected $resultRedirect;

    /**
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultRedirect
     * @param ResourceConnection $resource
     */
    public function __construct(
        RequestInterface $request,
        ManagerInterface $messageManager,
        ResultFactory $resultRedirect,
        ResourceConnection $resource
    ) {
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->resultRedirect = $resultRedirect;
        $this->resource = $resource;
    }

    /**
     * Change nickname to 'anonymous' if is empty
     */
    public function beforeExecute()
    {
        $requestParams = $this->request->getPostValue();
        if (!$requestParams) {
            $this->messageManager->addErrorMessage('Invalid query. Perhaps the file you uploaded is too big.');
            $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/*');
        } elseif (empty($requestParams['nickname'])) {
            $requestParams['nickname'] = 'anonymous';
            $this->request->setPostValue($requestParams);
        }
    }
}
