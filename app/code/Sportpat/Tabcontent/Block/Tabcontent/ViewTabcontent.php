<?php

namespace Sportpat\Tabcontent\Block\Tabcontent;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Sportpat\Tabcontent\Block\ImageBuilder;

/**
 * @api
 */
class ViewTabcontent extends Template
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
     * get current Manage Content
     *
     * @return \Sportpat\Tabcontent\Api\Data\TabcontentInterface
     */
    public function getCurrentTabcontent()
    {
        return $this->coreRegistry->registry('current_tabcontent');
    }
    /**
     * @param $entity
     * @param $imageId
     * @param array $attributes
     * @return \Sportpat\Tabcontent\Block\Image
     */
    public function getImage($entity, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setEntity($entity)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }
}
