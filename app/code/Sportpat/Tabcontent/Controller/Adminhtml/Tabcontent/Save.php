<?php
namespace Sportpat\Tabcontent\Controller\Adminhtml\Tabcontent;

use Sportpat\Tabcontent\Api\TabcontentRepositoryInterface;
use Sportpat\Tabcontent\Api\Data\TabcontentInterface;
use Sportpat\Tabcontent\Api\Data\TabcontentInterfaceFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Sportpat\Tabcontent\Model\UploaderPool;

/**
 * Class Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action
{
    /**
     * Manage Content factory
     * @var TabcontentInterfaceFactory
     */
    protected $tabcontentFactory;
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
     * Uploader pool
     * @var UploaderPool
     */
    protected $uploaderPool;
    /**
     * Core registry
     * @var Registry
     */
    protected $registry;
    /**
     * Manage Content repository
     * @var TabcontentRepositoryInterface
     */
    protected $tabcontentRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param TabcontentInterfaceFactory $tabcontentFactory
     * @param TabcontentRepositoryInterface $tabcontentRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param UploaderPool $uploaderPool
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        TabcontentInterfaceFactory $tabcontentFactory,
        TabcontentRepositoryInterface $tabcontentRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        UploaderPool $uploaderPool,
        Registry $registry
    ) {
        $this->tabcontentFactory = $tabcontentFactory;
        $this->tabcontentRepository = $tabcontentRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->registry = $registry;
        $this->uploaderPool = $uploaderPool;
        parent::__construct($context);
    }

    /**
     * run the action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var TabcontentInterface $tabcontent */
        $tabcontent = null;
        $postData = $this->getRequest()->getPostValue();
        $data = $postData;
        $id = !empty($data['tabcontent_id']) ? $data['tabcontent_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $tabcontent = $this->tabcontentRepository->get((int)$id);
            } else {
                unset($data['tabcontent_id']);
                $tabcontent = $this->tabcontentFactory->create();
            }
            $image = $this->uploaderPool->getUploader('image')->uploadFileAndGetName('image', $data);
            $data['image'] = $image;
            $this->dataObjectHelper->populateWithArray($tabcontent, $data, TabcontentInterface::class);
            $this->tabcontentRepository->save($tabcontent);
            $this->messageManager->addSuccessMessage(__('You saved the Manage Content'));
            $this->dataPersistor->clear('sportpat_tabcontent_tabcontent');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('*/*/edit', ['tabcontent_id' => $tabcontent->getId()]);
            } else {
                $resultRedirect->setPath('*/*');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('sportpat_tabcontent_tabcontent', $postData);
            $resultRedirect->setPath('*/*/edit', ['tabcontent_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Manage Content'));
            $this->dataPersistor->set('sportpat\tabcontent_tabcontent', $postData);
            $resultRedirect->setPath('*/*/edit', ['tabcontent_id' => $id]);
        }
        return $resultRedirect;
    }
}
