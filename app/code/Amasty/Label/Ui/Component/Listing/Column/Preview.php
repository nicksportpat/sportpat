<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Ui\Component\Listing\Column;

use Amasty\Label\Helper\Config;
use Amasty\Label\Model\LabelsFactory;
use Amasty\Label\Model\Labels;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\LayoutFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Preview
 * @package Amasty\Label\Ui\Component\Listing\Column
 */
class Preview extends Column
{
    /**
     * @var Config
     */
    private $helper;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Visibility
     */
    private $productVisibility;

    /**
     * @var LabelsFactory
     */
    private $labelsFactory;

    /**
     * @var null|Product
     */
    private $product = null;

    public function __construct(
        ContextInterface $context,
        Config $helper,
        LayoutFactory $layoutFactory,
        CollectionFactory $productCollectionFactory,
        Visibility $productVisibility,
        UiComponentFactory $uiComponentFactory,
        LabelsFactory $labelsFactory,
        array $components = [],
        array $data = []
    ) {
        $this->helper = $helper;
        $this->layoutFactory = $layoutFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->labelsFactory = $labelsFactory;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $this->createPreviewProduct();

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['label_id'])) {
                    $label = $this->labelsFactory->create()
                        ->addData($item);
                    $label->setId($item['label_id']);
                    $config = $this->getData();
                    $mode = isset($config['config']['labelType'])
                        ? $config['config']['labelType']
                        : 'cat';
                    $item[$this->getData('name')] = $this->generateLabel($label, $mode);
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param Labels $label
     * @param $mode
     *
     * @return string
     */
    private function generateLabel(Labels $label, $mode)
    {
        if (!$label || !$label->getId() || !$this->product) {
            return '';
        }

        $block = $this->layoutFactory->create()->createBlock(
            \Amasty\Label\Block\Label::class,
            'amasty.label',
            [ 'data' => [] ]
        );

        $label->init($this->product, $mode);
        $html = $block
            ->setLabel($label)
            ->toHtml();

        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $html = str_replace('display: none;', '', $html);

        return $html;
    }

    private function createPreviewProduct()
    {
        /** @var $collection Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->productVisibility->getVisibleInCatalogIds());
        $collection->getSelect()->limit(1);

        $this->product = $collection->getFirstItem();
    }
}
