<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Controller\Ajax;

use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Amasty\Label\Model\LabelViewer;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Class Label
 * @package Amasty\Label\Controller\Ajax
 */
class Label extends Action
{
    /**
     * @var LabelViewer
     */
    private $labelViewer;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LabelViewer $labelViewer,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger,
        Context $context
    ) {
        parent::__construct($context);
        $this->labelViewer = $labelViewer;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $result = [];
        try {
            $result['labels'] = $this->labelViewer->renderProductLabel(
                $this->productRepository->getById((int)$this->getRequest()->getParam('product_id')),
                (int)$this->getRequest()->getParam('in_product_list') ? 'category' : 'product'
            );
        } catch (NoSuchEntityException $noSuchEntityException) {
            $this->logger->debug($noSuchEntityException->getMessage());
        }

        /** @var ResultJson $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);

        return $resultJson;
    }
}
