<?php
namespace Sportpat\Tabcontent\Controller\Tabcontent;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Sportpat\Tabcontent\Api\TabcontentRepositoryInterface;
use Sportpat\Tabcontent\Model\Tabcontent\Url as UrlModel;
use Magento\Store\Model\StoreManagerInterface;

class View extends Action
{
    /**
     * @var string
     */
    const BREADCRUMBS_CONFIG_PATH = 'sportpat_tabcontent/tabcontent/breadcrumbs';
    /**
     * @var \Sportpat\Tabcontent\Api\TabcontentRepositoryInterface
     */
    protected $tabcontentRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Sportpat\Tabcontent\Model\Tabcontent\Url
     */
    protected $urlModel;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param Context $context
     * @param TabcontentRepositoryInterface $tabcontentRepository
     * @param Registry $coreRegistry
     * @param UrlModel $urlModel
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        TabcontentRepositoryInterface $tabcontentRepository,
        Registry $coreRegistry,
        UrlModel $urlModel,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->tabcontentRepository = $tabcontentRepository;
        $this->coreRegistry = $coreRegistry;
        $this->urlModel = $urlModel;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $tabcontentId = (int)$this->getRequest()->getParam('id');
            $tabcontent = $this->tabcontentRepository->get($tabcontentId);

            if (!$tabcontent->getIsActive()) {
                throw new \Exception();
            }
            $validStores = [$this->storeManager->getStore()->getId(), 0];
            if (!count(array_intersect($validStores, $tabcontent->getStoreId()))) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            /** @var Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');
            return $resultForward;
        }

        $this->coreRegistry->register('current_tabcontent', $tabcontent);
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set($tabcontent->getTitle);
        $pageMainTitle = $resultPage->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle && $pageMainTitle instanceof \Magento\Theme\Block\Html\Title) {
            $pageMainTitle->setPageTitle($tabcontent->getTitle());
        }
        if ($this->scopeConfig->isSetFlag(self::BREADCRUMBS_CONFIG_PATH, ScopeInterface::SCOPE_STORE)) {
            /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbsBlock */
            $breadcrumbsBlock = $resultPage->getLayout()->getBlock('breadcrumbs');
            if ($breadcrumbsBlock) {
                $breadcrumbsBlock->addCrumb(
                    'home',
                    [
                        'label' => __('Home'),
                        'link'  => $this->_url->getUrl('')
                    ]
                );
                $breadcrumbsBlock->addCrumb(
                    'tabcontents',
                    [
                        'label' => __('Manage Contents'),
                        'link'  => $this->urlModel->getListUrl()
                    ]
                );
                $breadcrumbsBlock->addCrumb(
                    'tabcontent-' . $tabcontent->getId(),
                    [
                        'label' => $tabcontent->getTitle()
                    ]
                );
            }
        }
        return $resultPage;
    }
}
