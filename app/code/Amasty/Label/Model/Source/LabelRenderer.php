<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\Source;

use Amasty\Label\Model\ResourceModel\Labels\CollectionFactory;

/**
 * Class LabelRenderer
 * @package Amasty\Label\Model\Source
 */
class LabelRenderer implements \Magento\Framework\Option\ArrayInterface, \Magento\Config\Model\Config\CommentInterface
{
    /**
     * @var CollectionFactory
     */
    private $labelCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Backend\Block\Context
     */
    private $context;

    /**
     * @var \Amasty\Label\Helper\Config
     */
    private $helper;

    public function __construct(
        CollectionFactory $labelCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Label\Helper\Config $helper,
        \Magento\Backend\Block\Context $context
    ) {
        $this->labelCollectionFactory = $labelCollectionFactory;
        $this->storeManager = $storeManager;
        $this->context = $context;
        $this->helper = $helper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->labelCollectionFactory->create()
            ->addFieldToFilter('stores', ['like' => "%$storeId%"])
            ->addFieldToFilter('stock_status', 1)
            ->setOrder('pos', 'asc');
        $labels = [['value' => 0, 'label' => __('-- Please select --')]];
        foreach ($collection as $label) {
            $labels[] = [
                'value' => $label->getId(),
                'label' => $label->getName()
            ];
        }

        return $labels;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $labels = [];
        foreach ($this->toOptionArray() as $label) {
            $labels[$label['value']] = $label['label'];
        }

        return $labels;
    }

    /**
     * @param string $currentValue
     * @return string
     */
    public function getCommentText($currentValue = '')
    {
        $labelId = $this->helper->getModuleConfig('stock_status/out_of_stock_only')
            ? $this->helper->getModuleConfig('stock_status/default_label')
            : 0;

        if ($this->helper->isLabelExist($labelId)) {
            $url = $this->context->getUrlBuilder()->getUrl('amasty_label/labels/edit', ['id' => $labelId]);
        } else {
            $url = $this->context->getUrlBuilder()->getUrl('amasty_label/labels/new');
        }

        $comment = __(
            'Set \'Yes\' to show only \'Out of Stock\' label and hide all other active labels if the item is Out of Stock.'
            . ' Please click <a target="_blank" href="%1">here</a> to manage the \'Out of Stock\' label display.',
            $url
        );

        return $comment;
    }
}
