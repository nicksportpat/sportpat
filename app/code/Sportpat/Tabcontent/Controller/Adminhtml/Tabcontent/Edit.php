<?php
namespace Sportpat\Tabcontent\Controller\Adminhtml\Tabcontent;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Sportpat\Tabcontent\Api\TabcontentRepositoryInterface;

class Edit extends Action
{
    /**
     * @var TabcontentRepositoryInterface
     */
    private $tabcontentRepository;
    /**
     * @var Registry
     */
    private $registry;

    /**
     * Edit constructor.
     * @param Context $context
     * @param TabcontentRepositoryInterface $tabcontentRepository
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        TabcontentRepositoryInterface $tabcontentRepository,
        Registry $registry
    ) {
        $this->tabcontentRepository = $tabcontentRepository;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * get current Manage Content
     *
     * @return null|\Sportpat\Tabcontent\Api\Data\TabcontentInterface
     */
    private function initTabcontent()
    {
        $tabcontentId = $this->getRequest()->getParam('tabcontent_id');
        try {
            $tabcontent = $this->tabcontentRepository->get($tabcontentId);
        } catch (NoSuchEntityException $e) {
            $tabcontent = null;
        }
        $this->registry->register('current_tabcontent', $tabcontent);
        return $tabcontent;
    }

    /**
     * Edit or create Manage Content
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $tabcontent = $this->initTabcontent();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Sportpat_Tabcontent::tabcontent_tabcontent');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Contents'));

        if ($tabcontent === null) {
            $resultPage->getConfig()->getTitle()->prepend(__('New Manage Content'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend($tabcontent->getTitle());
        }
        return $resultPage;
    }
}
