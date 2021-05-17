<?php
namespace Sportpat\OrderSync\Controller\Adminhtml\Syncedorder;

use Sportpat\OrderSync\Api\SyncedorderRepositoryInterface;
use Sportpat\OrderSync\Api\Data\SyncedorderInterface;
use Sportpat\OrderSync\Model\ResourceModel\Syncedorder as SyncedorderResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class InlineEdit
 */
class InlineEdit extends Action
{
    /**
     * Synced Order repository
     * @var SyncedorderRepositoryInterface
     */
    protected $syncedorderRepository;
    /**
     * Data object processor
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * Data object helper
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * JSON Factory
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * Synced Order resource model
     * @var SyncedorderResourceModel
     */
    protected $syncedorderResourceModel;

    /**
     * constructor
     * @param Context $context
     * @param SyncedorderRepositoryInterface $syncedorderRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param JsonFactory $jsonFactory
     * @param SyncedorderResourceModel $syncedorderResourceModel
     */
    public function __construct(
        Context $context,
        SyncedorderRepositoryInterface $syncedorderRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        JsonFactory $jsonFactory,
        SyncedorderResourceModel $syncedorderResourceModel
    ) {
        $this->syncedorderRepository = $syncedorderRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->jsonFactory = $jsonFactory;
        $this->syncedorderResourceModel = $syncedorderResourceModel;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $syncedorderId) {
            /** @var \Sportpat\OrderSync\Model\Syncedorder|\Sportpat\OrderSync\Api\Data\SyncedorderInterface $syncedorder */
            try {
                $syncedorder = $this->syncedorderRepository->get((int)$syncedorderId);
                $syncedorderData = $postItems[$syncedorderId];
                $this->dataObjectHelper->populateWithArray($syncedorder, $syncedorderData, SyncedorderInterface::class);
                $this->syncedorderResourceModel->saveAttribute($syncedorder, array_keys($syncedorderData));
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithSyncedorderId($syncedorder, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithSyncedorderId($syncedorder, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithSyncedorderId(
                    $syncedorder,
                    __('Something went wrong while saving the Synced Order.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Synced Order id to error message
     *
     * @param \Sportpat\OrderSync\Api\Data\SyncedorderInterface $syncedorder
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithSyncedorderId(SyncedorderInterface $syncedorder, $errorText)
    {
        return '[Synced Order ID: ' . $syncedorder->getId() . '] ' . $errorText;
    }
}
