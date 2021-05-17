<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model;

use Amasty\Label\Api\Data\LabelInterface;
use Amasty\Label\Block\Label as LabelBlock;
use Amasty\Label\Model\ResourceModel\Labels\CollectionFactory;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Customer\Model\Session;
use Magento\Framework\Profiler;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

class LabelViewer
{
    const MODE_CATEGORY = 'category';

    const MODE_PRODUCT_PAGE = 'product';

    /**
     * @var \Amasty\Label\Model\ResourceModel\Labels\Collection|null
     */
    private $activeLabelCollection = null;

    /**
     * @var bool|null
     */
    private $showSeveralLabels = null;

    /**
     * @var int|null
     */
    private $maxLabelCount = null;

    /**
     * @var Configurable
     */
    private $productTypeConfigurable;

    /**
     * @var CollectionFactory
     */
    private $labelCollectionFactory;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var \Amasty\Label\Model\ResourceModel\Index
     */
    private $labelIndex;

    /**
     * @var \Amasty\Label\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var array
     */
    private $forParentEnabled = [];

    /**
     * @var null|LabelBlock
     */
    private $labelBlock = null;

    /**
     * @var array
     */
    private $productIds;

    /**
     * @var array
     */
    private $labelsIds;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        Configurable $catalogProductTypeConfigurable,
        CollectionFactory $labelCollectionFactory,
        \Amasty\Base\Model\Serializer $serializer,
        Session $customerSession,
        \Amasty\Label\Model\ResourceModel\Index $labelIndex,
        \Amasty\Label\Helper\Config $config,
        array $productIds = []
    ) {
        $this->productTypeConfigurable = $catalogProductTypeConfigurable;
        $this->labelCollectionFactory = $labelCollectionFactory;
        $this->serializer = $serializer;
        $this->customerSession = $customerSession;
        $this->labelIndex = $labelIndex;
        $this->config = $config;
        $this->layout = $layout;
        $this->productIds = $productIds;
    }

    /**
     * @param Product $product
     * @param string $mode
     * @param bool $shouldMove
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function renderProductLabel(Product $product, $mode = self::MODE_CATEGORY, $shouldMove = false)
    {
        $html = '';
        $appliedLabelIds = [];
        $applied = 0;
        $maxLabelCount = $this->getMaxLabelCount();
        $labelCollection = $this->getFullLabelCollection();
        Profiler::start('__RenderAmastyProductLabel__');

        foreach ($this->getLabelIds($product->getId(), $product->getStoreId()) as $labelId) {
            if ($applied == $maxLabelCount) {
                break;
            }

            $label = $labelCollection->getItemById($labelId);

            if (!$label) {
                continue;
            }

            if ($this->validateNonProductDependConditions($label, $applied)) {
                continue;
            }

            $label->setShouldMove($shouldMove);
            $label->init($product, $mode);

            if ($this->addLabelToApplied($label, $appliedLabelIds)) {
                $applied++;
                $html .= $this->generateHtml($label);
            }
        }

        /* apply label from child products*/
        if ($applied !== $maxLabelCount
            && in_array($product->getTypeId(), [Grouped::TYPE_CODE, Configurable::TYPE_CODE])
            && $this->isLabelForParentEnabled($product->getStoreId())
        ) {
            $usedProds = $this->getUsedProducts($product);

            foreach ($usedProds as $child) {
                foreach ($this->getLabelIds($child->getId(), $child->getStoreId()) as $labelId) {
                    /** @var Labels $label */
                    if ($applied == $maxLabelCount) {
                        break;
                    }

                    $label = $labelCollection->getItemById($labelId);

                    if (!$label->getUseForParent()
                        || $this->validateNonProductDependConditions($label, $applied)
                        || array_key_exists($label->getId(), $appliedLabelIds) // (remove duplicated)
                    ) {
                        continue;
                    }

                    $label->setShouldMove($shouldMove);
                    $label->init($child, $mode, $product);

                    if ($this->addLabelToApplied($label, $appliedLabelIds)) {
                        $applied++;
                        $html .= $this->generateHtml($label);
                    }
                }
            }
        }

        Profiler::stop('__RenderAmastyProductLabel__');

        return $html;
    }

    /**
     * @param \Amasty\Label\Model\Labels $label
     * @param $appliedLabelIds
     *
     * @return bool
     */
    private function addLabelToApplied(Labels $label, &$appliedLabelIds)
    {
        $position = $label->getMode() == 'cat' ? $label->getCatPos() : $label->getProdPos();

        if (!$this->isShowSeveralLabels()) {
            if (array_search($position, $appliedLabelIds) !== false) {
                return false;
            }
        }

        $appliedLabelIds[$label->getId()] = $position;

        return true;
    }

    /**
     * @param \Amasty\Label\Model\Labels $label
     * @param bool $applied
     * @return bool
     */
    private function validateNonProductDependConditions(Labels $label, &$applied)
    {
        if ($label->getIsSingle() === '1' && $applied) {
            return true;
        }

        // need this condition, because in_array returns true for NOT LOGGED IN customers
        if ($label->getCustomerGroupEnabled()
            && !$this->checkCustomerGroupCondition($label)
        ) {
            return true;
        }

        if (!$label->checkDateRange()) {
            return true;
        }

        return false;
    }

    /**
     * if anyone label has setting - UseForParent - check all
     * @param int $storeId
     * @return bool
     */
    private function isLabelForParentEnabled($storeId)
    {
        if (!isset($this->forParentEnabled[$storeId])) {
            $condition[] = [
                'finset' => [$storeId]
            ];
            $collection = $this->labelCollectionFactory->create()
                ->addActiveFilter()
                ->addFieldToFilter('stores', $condition)
                ->addFieldToFilter(LabelInterface::USE_FOR_PARENT, 1)
                ->setPageSize(1);
            $this->forParentEnabled[$storeId] = (bool) $collection->getSize();
        }

        return $this->forParentEnabled[$storeId];
    }

    /**
     * @param Labels $label
     * @return bool
     */
    private function checkCustomerGroupCondition(Labels $label)
    {
        if (!$label->hasData(LabelInterface::CUSTOMER_GROUP_VALID)) {
            $groups = $label->getData('customer_group_ids');

            if ($groups === '') {
                return true;
            }

            $groups = $this->serializer->unserialize($groups);
            $customerGroupValid = in_array(
                (int)$this->customerSession->getCustomerGroupId(),
                $groups
            );
            $label->setData(LabelInterface::CUSTOMER_GROUP_VALID, $customerGroupValid);
        }

        return $label->getData(LabelInterface::CUSTOMER_GROUP_VALID);
    }

    /**
     * generate block with label configuration
     * @param Labels $label
     * @return string
     */
    private function generateHtml(Labels $label)
    {
        if ($this->labelBlock === null) {
            $this->labelBlock = $this->layout->createBlock(LabelBlock::class);
        }

        return $this->labelBlock->setLabel($label)->toHtml();
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getLabelIds($productId, $storeId)
    {
        $labelIds = $this->labelIndex->getIdsFromIndex($productId, $storeId);

        if (empty($labelIds)) {
            return [];
        }

        return array_unique($labelIds);
    }

    /**
     * @return $this|ResourceModel\Labels\Collection|null
     */
    private function getFullLabelCollection()
    {
        if ($this->activeLabelCollection === null) {
            $this->activeLabelCollection = $this->labelCollectionFactory->create()
                ->addActiveFilter()
                ->setOrder('pos', 'asc')
                ->load();
        }

        return $this->activeLabelCollection;
    }

    /**
     * @param Product $product
     * @return array|\Magento\Catalog\Api\Data\ProductInterface[]
     */
    private function getUsedProducts(Product $product)
    {
        if ($product->getTypeId() == Configurable::TYPE_CODE) {
            return $this->productTypeConfigurable->getUsedProducts($product);
        } else { // product is grouped
            return $product->getTypeInstance(true)->getAssociatedProducts($product);
        }
    }

    /**
     * @return bool
     */
    private function isShowSeveralLabels()
    {
        if ($this->showSeveralLabels === null) {
            $this->showSeveralLabels = $this->config->getModuleConfig('display/show_several_on_place');
        }

        return (bool)$this->showSeveralLabels;
    }

    /**
     * @return int
     */
    private function getMaxLabelCount()
    {
        if ($this->maxLabelCount === null) {
            $this->maxLabelCount = $this->config->getMaxLabels();
        }

        return $this->maxLabelCount;
    }
}
