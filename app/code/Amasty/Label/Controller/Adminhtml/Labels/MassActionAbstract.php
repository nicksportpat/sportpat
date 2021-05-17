<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Controller\Adminhtml\Labels;

use Amasty\Label\Api\Data\LabelInterface;
use Magento\Backend\App\Action\Context;
use Amasty\Label\Model\ResourceModel\Labels\CollectionFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultInterface;
use Amasty\Label\Api\LabelRepositoryInterface;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class MassDelete
 */
abstract class MassActionAbstract extends \Magento\Backend\App\Action
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Amasty\Label\Model\Indexer\LabelIndexer
     */
    private $labelIndexer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var LabelRepositoryInterface
     */
    protected $labelRepository;

    /**
     * @var Filter
     */
    private $filter;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        \Amasty\Label\Model\Indexer\LabelIndexer $labelIndexer,
        LabelRepositoryInterface $labelRepository,
        LoggerInterface $logger,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->labelIndexer = $labelIndexer;
        $this->logger = $logger;
        $this->labelRepository = $labelRepository;
        $this->filter = $filter;
    }

    /**
     * @param LabelInterface $label
     *
     * @return void
     */
    abstract protected function itemAction($label);

    /**
     * Mass action execution
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $this->filter->applySelectionOnTargetProvider(); // compatibility with Mass Actions on Magento 2.1.0
        /** @var \Amasty\Label\Model\ResourceModel\Labels\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection = $this->filter->getCollection($collection);

        $collectionSize = $collection->getSize();
        if ($collectionSize) {
            try {
                foreach ($collection->getItems() as $reminder) {
                    $this->itemAction($reminder);
                }
                $this->invalidateIndex();

                $this->messageManager->addSuccessMessage($this->getSuccessMessage($collectionSize));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($this->getErrorMessage());
                $this->logger->critical($e);
            }
        }

        return $this->resultRedirectFactory->create()
            ->setUrl($this->_redirect->getRefererUrl());
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getErrorMessage()
    {
        return __('We can\'t change item right now. Please review the log and try again.');
    }

    /**
     * @param int $collectionSize
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getSuccessMessage($collectionSize = 0)
    {
        if ($collectionSize) {
            return __('A total of %1 record(s) have been changed.', $collectionSize);
        }

        return __('No records have been changed.');
    }

    /**
     * invalidate amasty label index
     */
    protected function invalidateIndex()
    {
        $this->labelIndexer->invalidateIndex();
    }

    /**
     * @return \Amasty\Label\Model\ResourceModel\Labels\Collection
     */
    protected function getCollection()
    {
        $ids = $this->getRequest()->getParam('label_ids');
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('label_id', $ids);

        return $collection;
    }
}
