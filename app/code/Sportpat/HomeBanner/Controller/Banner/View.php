<?php
namespace Sportpat\HomeBanner\Controller\Banner;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\View\Result\Page;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Sportpat\HomeBanner\Api\BannerRepositoryInterface;
use Sportpat\HomeBanner\Model\Banner\Url as UrlModel;
use Magento\Store\Model\StoreManagerInterface;

class View extends Action
{
    /**
     * @var string
     */
    const BREADCRUMBS_CONFIG_PATH = 'sportpat_home_banner/banner/breadcrumbs';
    /**
     * @var \Sportpat\HomeBanner\Api\BannerRepositoryInterface
     */
    protected $bannerRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Sportpat\HomeBanner\Model\Banner\Url
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
     * @param BannerRepositoryInterface $bannerRepository
     * @param Registry $coreRegistry
     * @param UrlModel $urlModel
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        BannerRepositoryInterface $bannerRepository,
        Registry $coreRegistry,
        UrlModel $urlModel,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->bannerRepository = $bannerRepository;
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
            $bannerId = (int)$this->getRequest()->getParam('id');
            $banner = $this->bannerRepository->get($bannerId);

            if (!$banner->getIsActive()) {
                throw new \Exception();
            }
            $validStores = [$this->storeManager->getStore()->getId(), 0];
            if (!count(array_intersect($validStores, $banner->getStoreId()))) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            /** @var Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');
            return $resultForward;
        }

        $this->coreRegistry->register('current_banner', $banner);
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set($banner->getBannerName);
        $pageMainTitle = $resultPage->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle && $pageMainTitle instanceof \Magento\Theme\Block\Html\Title) {
            $pageMainTitle->setPageTitle($banner->getBannerName());
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
                    'banners',
                    [
                        'label' => __('Banners'),
                        'link'  => $this->urlModel->getListUrl()
                    ]
                );
                $breadcrumbsBlock->addCrumb(
                    'banner-' . $banner->getId(),
                    [
                        'label' => $banner->getBannerName()
                    ]
                );
            }
        }
        return $resultPage;
    }
}
