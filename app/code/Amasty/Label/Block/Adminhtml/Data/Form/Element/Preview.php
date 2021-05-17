<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Block\Adminhtml\Data\Form\Element;

use Magento\Framework\Escaper;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\View\LayoutFactory;

/**
 * Class Preview
 * @package Amasty\Label\Block\Adminhtml\Data\Form\Element
 */
class Preview extends \Magento\Framework\Data\Form\Element\Text
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;

    /**
     * @var \Amasty\Label\Model\LabelsFactory
     */
    private $labelsFactory;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    private $productVisibility;

    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Amasty\Label\Model\LabelsFactory $labelsFactory,
        LayoutFactory $layoutFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->assetRepo = $assetRepo;
        $this->labelsFactory = $labelsFactory;
        $this->layoutFactory = $layoutFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->productVisibility = $productVisibility;
    }

    public function getElementHtml()
    {
        $html = '<div class="preview" id="' . $this->getHtmlId() . '">';
        $html .= '<div class="preview-image">';
        $html .= '<img src="' . $this->getExampleFile() . '">';
        $html .= $this->generateLabel();
        $html .= '</div>';
        $html .= '<p class="note" id="note_preview"><span>' .
            __(
                'Please click %1 class="update-preview">here</a> to update the preview and save the changes.',
                '<a onclick="jQuery(\'#save_and_continue_edit\').click()"'
            ) .
            '</span></p>';

        $html .= '</div>';

        $html .= $this->getJsHtml($this->getHtmlId());
        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * @return string
     */
    private function getExampleFile()
    {
        $name = 'Amasty_Label::images/example.jpg';
        $params = [];

        return $this->assetRepo->getUrlWithParams($name, $params);
    }

    /**
     * @param $field
     *
     * @return string
     */
    private function getJsHtml($field)
    {
        $html = '<script>
            require([
              "jquery",
              "Amasty_Label/js/amlabel"
            ], function ($) {
               $("#' . $field . '").amLabelPreview();
            });
        </script>';

        return $html;
    }

    /**
     * @return string
     */
    private function generateLabel()
    {
        $label = $this->coreRegistry->registry('current_amasty_label');
        if (!$label || !$label->getId()) {
            return '';
        }

        $layout = $this->layoutFactory->create();
        $block = $layout->createBlock(
            \Amasty\Label\Block\Label::class,
            'amasty.label',
            [ 'data' => [] ]
        );

        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->productVisibility->getVisibleInCatalogIds());
        $collection->getSelect()->limit(1);

        $product = $collection->getFirstItem();

        $mode = ($this->getHtmlId() == 'labels_prod_preview') ? 'prod' : 'category';
        $label->init($product, $mode);
        $html = $block->setLabel($label)->toHtml();

        return $html;
    }
}
