<?php

namespace Sportpat\HomeBanner\Block\Banner;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Sportpat\HomeBanner\Block\ImageBuilder;

/**
 * @api
 */
class ViewBanner extends Template
{
    /**
     * @var Registry
     */
    private $coreRegistry;
    /**
     * @var ImageBuilder
     */
    private $imageBuilder;
    /**
     * @param Context $context
     * @param Registry $registry
     * @param $imageBuilder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ImageBuilder $imageBuilder,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->imageBuilder = $imageBuilder;
        parent::__construct($context, $data);
    }

    /**
     * get current Banner
     *
     * @return \Sportpat\HomeBanner\Api\Data\BannerInterface
     */
    public function getCurrentBanner()
    {
        return $this->coreRegistry->registry('current_banner');
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
}
