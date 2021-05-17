<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Controller\Adminhtml\Labels;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Reindex
 * @package Amasty\Label\Controller\Adminhtml\Labels
 */
class Reindex extends \Magento\Backend\App\Action
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Amasty\Label\Model\Indexer\LabelIndexer
     */
    private $labelIndexer;

    /**
     * Reindex constructor.
     * @param Action\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Amasty\Label\Model\Indexer\LabelIndexer $labelIndexer
     */
    public function __construct(
        Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Amasty\Label\Model\Indexer\LabelIndexer $labelIndexer
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->labelIndexer = $labelIndexer;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->labelIndexer->executeByLabelId($id);
                $this->messageManager->addSuccessMessage(__('You have reindexed the label.'));
                $this->_redirect('amasty_label/*/edit', ['id' =>  $id]);
                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t reindex label right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('amasty_label/*/edit', ['id' =>  $id]);
                return;
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a item to reindex.'));
        $this->_redirect('amasty_label/*/');
    }
}
