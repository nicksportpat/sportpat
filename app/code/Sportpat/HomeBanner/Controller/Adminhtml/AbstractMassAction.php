<?php
namespace Sportpat\HomeBanner\Controller\Adminhtml;

use Sportpat\HomeBanner\Ui\Provider\CollectionProviderInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Sportpat\HomeBanner\Api\ExecutorInterface;

abstract class AbstractMassAction extends Action
{

    /**
     * Mass Action filter
     * @var Filter
     */
    protected $filter;

    /**
     * Collection provider
     * @var CollectionProviderInterface
     */
    protected $collectionProvider;

    /**
     * Action success message
     * @var string
     */
    protected $successMessage;

    /**
     * Action error message
     * @var string
     */
    protected $errorMessage;
    /**
     * @var ExecutorInterface
     */
    protected $executor;

    /**
     * constructor
     * @param Context $context
     * @param Filter $filter
     * @param CollectionProviderInterface $collectionProvider
     * @param string $successMessage
     * @param string $errorMessage
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionProviderInterface $collectionProvider,
        ExecutorInterface $executor,
        $successMessage,
        $errorMessage
    ) {
        $this->filter = $filter;
        $this->collectionProvider = $collectionProvider;
        $this->executor = $executor;
        $this->successMessage = $successMessage;
        $this->errorMessage = $errorMessage;
        parent::__construct($context);
    }

    /**
     * execute action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionProvider->getCollection());
            $collectionSize = $collection->getSize();
            foreach ($collection as $entity) {
                $this->executor->execute($entity->getId());
            }
            $this->messageManager->addSuccessMessage(__($this->successMessage, $collectionSize));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, $this->errorMessage);
        }
        $redirectResult = $this->resultRedirectFactory->create();
        $redirectResult->setPath('*/*/index');
        return $redirectResult;
    }
}
