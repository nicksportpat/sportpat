<?php
namespace Sportpat\HomeBanner\Controller\Adminhtml\Banner;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Sportpat\HomeBanner\Api\BannerRepositoryInterface;

class Edit extends Action
{
    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepository;
    /**
     * @var Registry
     */
    private $registry;

    /**
     * Edit constructor.
     * @param Context $context
     * @param BannerRepositoryInterface $bannerRepository
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        BannerRepositoryInterface $bannerRepository,
        Registry $registry
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * get current Banner
     *
     * @return null|\Sportpat\HomeBanner\Api\Data\BannerInterface
     */
    private function initBanner()
    {
        $bannerId = $this->getRequest()->getParam('banner_id');
        try {
            $banner = $this->bannerRepository->get($bannerId);
        } catch (NoSuchEntityException $e) {
            $banner = null;
        }
        $this->registry->register('current_banner', $banner);
        return $banner;
    }

    /**
     * Edit or create Banner
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $banner = $this->initBanner();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Sportpat_HomeBanner::homebanner_banner');
        $resultPage->getConfig()->getTitle()->prepend(__('Banners'));

        if ($banner === null) {
            $resultPage->getConfig()->getTitle()->prepend(__('New Banner'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend($banner->getBannerName());
        }
        return $resultPage;
    }
}
