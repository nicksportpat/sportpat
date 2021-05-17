<?php
namespace Sportpat\Tabcontent\Controller\Adminhtml\Tabcontent;

use Sportpat\Tabcontent\Api\TabcontentRepositoryInterface;
use Sportpat\Tabcontent\Api\Data\TabcontentInterface;
use Sportpat\Tabcontent\Model\ResourceModel\Tabcontent as TabcontentResourceModel;
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
     * Manage Content repository
     * @var TabcontentRepositoryInterface
     */
    protected $tabcontentRepository;
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
     * Manage Content resource model
     * @var TabcontentResourceModel
     */
    protected $tabcontentResourceModel;

    /**
     * constructor
     * @param Context $context
     * @param TabcontentRepositoryInterface $tabcontentRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param JsonFactory $jsonFactory
     * @param TabcontentResourceModel $tabcontentResourceModel
     */
    public function __construct(
        Context $context,
        TabcontentRepositoryInterface $tabcontentRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        JsonFactory $jsonFactory,
        TabcontentResourceModel $tabcontentResourceModel
    ) {
        $this->tabcontentRepository = $tabcontentRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->jsonFactory = $jsonFactory;
        $this->tabcontentResourceModel = $tabcontentResourceModel;
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

        foreach (array_keys($postItems) as $tabcontentId) {
            /** @var \Sportpat\Tabcontent\Model\Tabcontent|\Sportpat\Tabcontent\Api\Data\TabcontentInterface $tabcontent */
            try {
                $tabcontent = $this->tabcontentRepository->get((int)$tabcontentId);
                $tabcontentData = $postItems[$tabcontentId];
                $this->dataObjectHelper->populateWithArray($tabcontent, $tabcontentData, TabcontentInterface::class);
                $this->tabcontentResourceModel->saveAttribute($tabcontent, array_keys($tabcontentData));
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithTabcontentId($tabcontent, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithTabcontentId($tabcontent, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithTabcontentId(
                    $tabcontent,
                    __('Something went wrong while saving the Manage Content.')
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
     * Add Manage Content id to error message
     *
     * @param \Sportpat\Tabcontent\Api\Data\TabcontentInterface $tabcontent
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithTabcontentId(TabcontentInterface $tabcontent, $errorText)
    {
        return '[Manage Content ID: ' . $tabcontent->getId() . '] ' . $errorText;
    }
}
