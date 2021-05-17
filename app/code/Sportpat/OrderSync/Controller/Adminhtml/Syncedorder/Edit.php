<?php
namespace Sportpat\OrderSync\Controller\Adminhtml\Syncedorder;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Sportpat\OrderSync\Api\SyncedorderRepositoryInterface;

class Edit extends Action
{
    /**
     * @var SyncedorderRepositoryInterface
     */
    private $syncedorderRepository;
    /**
     * @var Registry
     */
    private $registry;

    /**
     * Edit constructor.
     * @param Context $context
     * @param SyncedorderRepositoryInterface $syncedorderRepository
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        SyncedorderRepositoryInterface $syncedorderRepository,
        Registry $registry
    ) {
        $this->syncedorderRepository = $syncedorderRepository;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * get current Synced Order
     *
     * @return null|\Sportpat\OrderSync\Api\Data\SyncedorderInterface
     */
    private function initSyncedorder()
    {
        $syncedorderId = $this->getRequest()->getParam('syncedorder_id');
        try {
            $syncedorder = $this->syncedorderRepository->get($syncedorderId);
        } catch (NoSuchEntityException $e) {
            $syncedorder = null;
        }
        $this->registry->register('current_syncedorder', $syncedorder);
        return $syncedorder;
    }

    /**
     * Edit or create Synced Order
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $syncedorder = $this->initSyncedorder();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Sportpat_OrderSync::ordersync_syncedorder');
        $resultPage->getConfig()->getTitle()->prepend(__('Synced Orders'));

        if ($syncedorder === null) {
            $resultPage->getConfig()->getTitle()->prepend(__('New Synced Order'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend($syncedorder->getMagentoOrderid());
        }
        return $resultPage;
    }
}
