<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Api\Data;

interface LabelIndexInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const INDEX_ID = 'index_id';
    const LABEL_ID = 'label_id';
    const PRODUCT_ID = 'product_id';
    const STORE_ID = 'store_id';
    /**#@-*/

    /**
     * @return int
     */
    public function getIndexId();

    /**
     * @param int $indexId
     *
     * @return \Amasty\Label\Api\Data\LabelIndexInterface
     */
    public function setIndexId($indexId);

    /**
     * @return int|null
     */
    public function getLabelId();

    /**
     * @param int|null $labelId
     *
     * @return \Amasty\Label\Api\Data\LabelIndexInterface
     */
    public function setLabelId($labelId);

    /**
     * @return int|null
     */
    public function getProductId();

    /**
     * @param int|null $productId
     *
     * @return \Amasty\Label\Api\Data\LabelIndexInterface
     */
    public function setProductId($productId);

    /**
     * @return int|null
     */
    public function getStoreId();

    /**
     * @param int|null $storeId
     *
     * @return \Amasty\Label\Api\Data\LabelIndexInterface
     */
    public function setStoreId($storeId);
}
