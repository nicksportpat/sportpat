<?php

namespace Sportpat\Tabcontent\Block\Tabcontent;

use Magento\Framework\UrlFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Pager;
use Sportpat\Tabcontent\Api\Data\TabcontentInterface;
use Sportpat\Tabcontent\Model\Tabcontent;
use Sportpat\Tabcontent\Model\ResourceModel\Tabcontent\CollectionFactory as TabcontentCollectionFactory;
use Sportpat\Tabcontent\Block\ImageBuilder;
use Sportpat\Tabcontent\Model\Tabcontent\Url;

/**
 * @api
 */
class ListTabcontent extends Template
{
    /**
     * @var TabcontentCollectionFactory
     */
    private $tabcontentCollectionFactory;
    /**
     * @var \Sportpat\Tabcontent\Model\ResourceModel\Tabcontent\Collection
     */
    private $tabcontents;
    /**
     * @var ImageBuilder
     */
    private $imageBuilder;
    /**
     * @var Url
     */
    private $urlModel;
    /**
     * @param Context $context
     * @param TabcontentCollectionFactory $tabcontentCollectionFactory
     * @param ImageBuilder $imageBuilder
     * @param Url $urlModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        TabcontentCollectionFactory $tabcontentCollectionFactory,
        ImageBuilder $imageBuilder,
        Url $urlModel,
        array $data = []
    ) {
        $this->tabcontentCollectionFactory = $tabcontentCollectionFactory;
        $this->imageBuilder = $imageBuilder;
        $this->urlModel = $urlModel;
        parent::__construct($context, $data);
    }

    /**
     * @return \Sportpat\Tabcontent\Model\ResourceModel\Tabcontent\Collection
     */
    public function getTabcontents()
    {
        if (is_null($this->tabcontents)) {
            $this->tabcontents = $this->tabcontentCollectionFactory->create()
                ->addFieldToFilter('is_active', TabcontentInterface::STATUS_ENABLED)
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->setOrder('title', 'ASC');
        }
        return $this->tabcontents;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager $pager */
        $pager = $this->getLayout()->createBlock(Pager::class, 'sportpat.tabcontent.tabcontent.list.pager');
        $pager->setCollection($this->getTabcontents());
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
     * @return \Sportpat\Tabcontent\Block\Image
    */
    public function getImage($entity, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setEntity($entity)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * @param TabcontentInterface $tabcontent
     * @return string
    */
    public function getTabcontentUrl(TabcontentInterface $tabcontent)
    {
        return $this->urlModel->getTabcontentUrl($tabcontent);
    }
}
