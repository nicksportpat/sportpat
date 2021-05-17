<?php

namespace Sportpat\HomeBanner\Model\Banner;

use Magento\Framework\UrlInterface;
use Sportpat\HomeBanner\Api\Data\BannerInterface;

class Url
{
    /**
     * url builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return string
     */
    public function getListUrl()
    {
        return $this->urlBuilder->getUrl('sportpat_home_banner/banner/index');
    }

    /**
     * @param BannerInterface $banner
     * @return string
     */
    public function getBannerUrl(BannerInterface $banner)
    {
        return $this->urlBuilder->getUrl('sportpat_home_banner/banner/view', ['id' => $banner->getId()]);
    }
}
