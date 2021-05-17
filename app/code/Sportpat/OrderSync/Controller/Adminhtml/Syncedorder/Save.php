<?php
namespace Sportpat\OrderSync\Controller\Adminhtml\Syncedorder;

use Sportpat\OrderSync\Api\SyncedorderRepositoryInterface;
use Sportpat\OrderSync\Api\Data\SyncedorderInterface;
use Sportpat\OrderSync\Api\Data\SyncedorderInterfaceFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;

/**
 * Class Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action
{
    /**
     * Synced Order factory
     * @var SyncedorderInterfaceFactory
     */
    protected $syncedorderFactory;
    /**
     * Data Object Processor
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * Data Object Helper
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * Data Persistor
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * Core registry
     * @var Registry
     */
    protected $registry;
    /**
     * Synced Order repository
     * @var SyncedorderRepositoryInterface
     */
    protected $syncedorderRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param SyncedorderInterfaceFactory $syncedorderFactory
     * @param SyncedorderRepositoryInterface $syncedorderRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        SyncedorderInterfaceFactory $syncedorderFactory,
        SyncedorderRepositoryInterface $syncedorderRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        Registry $registry
    ) {
        $this->syncedorderFactory = $syncedorderFactory;
        $this->syncedorderRepository = $syncedorderRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * run the action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var SyncedorderInterface $syncedorder */
        $syncedorder = null;
        $postData = $this->getRequest()->getPostValue();
        $data = $postData;
        $id = !empty($data['syncedorder_id']) ? $data['syncedorder_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $syncedorder = $this->syncedorderRepository->get((int)$id);
            } else {
                unset($data['syncedorder_id']);
                $syncedorder = $this->syncedorderFactory->create();
            }
            $this->dataObjectHelper->populateWithArray($syncedorder, $data, SyncedorderInterface::class);
            $this->syncedorderRepository->save($syncedorder);
            $this->messageManager->addSuccessMessage(__('You saved the Synced Order'));
            $this->dataPersistor->clear('sportpat_order_sync_syncedorder');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('*/*/edit', ['syncedorder_id' => $syncedorder->getId()]);
            } else {
                $resultRedirect->setPath('*/*');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('sportpat_order_sync_syncedorder', $postData);
            $resultRedirect->setPath('*/*/edit', ['syncedorder_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Synced Order'));
            $this->dataPersistor->set('sportpat\order_sync_syncedorder', $postData);
            $resultRedirect->setPath('*/*/edit', ['syncedorder_id' => $id]);
        }
        return $resultRedirect;
    }
}
