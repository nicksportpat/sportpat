<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\ResourceModel;

use Amasty\Label\Api\Data\LabelIndexInterface;
use \Amasty\Label\Api\Data\LabelInterface as LabelInterface;
use Amasty\Label\Model\LabelIndex;

/**
 * Class Index
 * @package Amasty\Label\Model\ResourceModel
 */
class Index extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const AMASTY_LABEL_INDEX_TABLE = 'amasty_label_index';

    /**
     * @var array
     */
    private $cache = [];

    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::AMASTY_LABEL_INDEX_TABLE, 'index_id');
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIdsFromIndex($productId, $storeId)
    {
        if (!isset($this->cache[$storeId])) {
            $this->generateCacheForStore($storeId);
        }

        $ids = $this->cache[$storeId][$productId] ?? '';

        return $ids ? explode(',', $ids) : [];
    }

    /**
     * @param int $storeId
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function generateCacheForStore($storeId)
    {
        $this->cache[$storeId] = [];
        $data = $this->getConnection()->fetchAll($this->getStoreQuery($storeId));
        foreach ($data as $item) {
            $this->cache[$storeId][$item[LabelIndex::PRODUCT_ID]] = $item[LabelIndex::LABEL_ID];
        }
    }

    /**
     * @param int $storeId
     *
     * @return \Magento\Framework\DB\Select
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getStoreQuery($storeId)
    {
        return $this->getConnection()
            ->select()
            ->from(
                $this->getMainTable(),
                [
                    LabelIndex::LABEL_ID => 'GROUP_CONCAT(' . LabelIndex::LABEL_ID . ')',
                    LabelIndex::PRODUCT_ID
                ]
            )
            ->distinct()
            ->where(LabelIndexInterface::STORE_ID . ' = ?', $storeId)
            ->group(LabelIndex::PRODUCT_ID);
    }

    /**
     * this method is not used now. Can be helpful in future
     * @param $labelIds
     * @param $entityId
     * @param $field
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkValidProductFromIndex($labelIds, $entityId, $field)
    {
        $query = $this->getConnection()
            ->select()
            ->from(
                $this->getMainTable(),
                LabelInterface::LABEL_ID
            )
            ->distinct()
            ->where(
                LabelInterface::LABEL_ID . ' IN (?)',
                $labelIds
            )
            ->where($field . ' = :'. $field);

        return $this->getConnection()->fetchAll($query, [$field => $entityId]);
    }

    /**
     * @param $labelsIds
     */
    public function cleanByLabelIds($labelsIds)
    {
        $query = $this->getConnection()->deleteFromSelect(
            $this->getConnection()
                ->select()
                ->from($this->getMainTable(), LabelInterface::LABEL_ID)
                ->where(LabelInterface::LABEL_ID . ' IN (?)', $labelsIds),
            $this->getMainTable()
        );

        $this->getConnection()->query($query);
    }

    /**
     * @param $productIds
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cleanByProductIds($productIds)
    {
        $query = $this->getConnection()->deleteFromSelect(
            $this->getConnection()
                ->select()
                ->from($this->getMainTable(), LabelInterface::LABEL_ID)
                ->where(LabelIndexInterface::PRODUCT_ID . ' IN (?)', $productIds),
            $this->getMainTable()
        );

        $this->getConnection()->query($query);
    }

    /**
     * @param $labelId
     * @return int|void
     */
    public function getCountLabelIndexes($labelId)
    {
        return $this->getCountIndex($labelId);
    }

    /**
     * @param int|string $labelId
     * @param bool $guest
     * @return int|void
     */
    public function getCountIndex($labelId)
    {
        $query = $this->getConnection()
            ->select()
            ->from($this->getMainTable(), LabelInterface::LABEL_ID)
            ->where(LabelInterface::LABEL_ID . ' IN (?)', $labelId);

        return count($this->getConnection()->fetchAll($query));
    }

    /**
     * @return $this
     */
    public function cleanAllIndex()
    {
         $query = "truncate ".$this->getMainTable();
        $this->getConnection()->query($query);

        return $this;
    }
    
    /**
     * @param array $data
     * @return $this
     */
    public function insertIndexData(array $data)
    {
        $this->getConnection()->insertOnDuplicate($this->getMainTable(), $data);

        return $this;
    }
}
