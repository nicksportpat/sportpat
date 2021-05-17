<?php

namespace Sportpat\HomeBanner\Block\Banner;

use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Pager;
use Sportpat\HomeBanner\Api\Data\BannerInterface;
use Sportpat\HomeBanner\Model\Banner;
use Sportpat\HomeBanner\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;
use Sportpat\HomeBanner\Block\ImageBuilder;
use Sportpat\HomeBanner\Model\Banner\Url;

/**
 * @api
 */
class ListBanner extends Template
{
    /**
     * @var BannerCollectionFactory
     */
    private $bannerCollectionFactory;
    /**
     * @var \Sportpat\HomeBanner\Model\ResourceModel\Banner\Collection
     */
    private $banners;
    /**
     * @var ImageBuilder
     */
    private $imageBuilder;
    /**
     * @var Url
     */
    private $urlModel;

    private $_storemanager;
    /**
     * @param Context $context
     * @param BannerCollectionFactory $bannerCollectionFactory
     * @param ImageBuilder $imageBuilder
     * @param Url $urlModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        BannerCollectionFactory $bannerCollectionFactory,
        ImageBuilder $imageBuilder,
        Url $urlModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->imageBuilder = $imageBuilder;
        $this->urlModel = $urlModel;
        $this->_storemanager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getStoreId()
    {
        return $this->_storemanager->getStore()->getId();
    }

    /**
     * @return \Sportpat\HomeBanner\Model\ResourceModel\Banner\Collection
     */
    public function getBanners()
    {
        if (is_null($this->banners)) {
            $this->banners = $this->bannerCollectionFactory->create()
                ->addFieldToFilter('is_active', BannerInterface::STATUS_ENABLED)
                ->addStoreFilter($this->getStoreId())
                ->setOrder('banner_order', 'ASC');
        }
        return $this->banners;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager $pager */
        $pager = $this->getLayout()->createBlock(Pager::class, 'sportpat.home_banner.banner.list.pager');
        $pager->setCollection($this->getBanners());
        $this->setChild('pager', $pager);
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param $entity
     * @param $imageId
     * @param array $attributes
     * @return \Sportpat\HomeBanner\Block\Image
    */
    public function getImage($entity, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setEntity($entity)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * @param BannerInterface $banner
     * @return string
    */
    public function getBannerUrl(BannerInterface $banner)
    {
        return $this->urlModel->getBannerUrl($banner);
    }
}
