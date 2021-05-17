<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model;

use Amasty\Base\Model\Serializer;
use Amasty\Label\Api\Data\LabelIndexInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class LabelIndex
 * @package Amasty\Label\Model
 */
class LabelIndex extends \Magento\Framework\Model\AbstractModel implements LabelIndexInterface
{
    /**
     * Label Index cache tag
     */
    const CACHE_TAG = 'amasty_label_index';
    public $_cacheTag = 'amasty_label_index';

    /**
     * {@inheritdoc}
     */
    public function getIndexId()
    {
        return $this->_getData(LabelIndexInterface::INDEX_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setIndexId($indexId)
    {
        $this->setData(LabelIndexInterface::INDEX_ID, $indexId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelId()
    {
        return $this->_getData(LabelIndexInterface::LABEL_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelId($labelId)
    {
        $this->setData(LabelIndexInterface::LABEL_ID, $labelId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->_getData(LabelIndexInterface::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($productId)
    {
        $this->setData(LabelIndexInterface::PRODUCT_ID, $productId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->_getData(LabelIndexInterface::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        $this->setData(LabelIndexInterface::STORE_ID, $storeId);

        return $this;
    }
}
