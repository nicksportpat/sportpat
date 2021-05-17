<?php
namespace Sportpat\HomeBanner\Model\ResourceModel\Banner;

use Sportpat\HomeBanner\Model\Banner;
use Sportpat\HomeBanner\Model\ResourceModel\AbstractCollection;

/**
 * @api
 */
class Collection extends AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Banner::class,
            \Sportpat\HomeBanner\Model\ResourceModel\Banner::class
        );
        $this->_map['fields']['store_id'] = 'store_table.store_id';
        $this->_map['fields']['banner_id'] = 'main_table.banner_id';
    }

    /**
     * after collection load
     */
    protected function _afterLoad()
    {
        $ids = [];
        foreach ($this as $item) {
            $ids[] = $item->getId();
        }
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                ['store_table' => $this->getTable('sportpat_home_banner_banner_store')]
            )->where('store_table.banner_id IN (?)', $ids);
            $result = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData['banner_id']][] = $storeData['store_id'];
                }
                foreach ($this as $item) {
                    $linkedId = $item->getData('banner_id');
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $item->setData('store_id', $storesData[$linkedId]);
                }
            }
        }
        return parent::_afterLoad();
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        if (!isset($this->joinFields['store'])) {
            $this->getSelect()->join(
                [
                    'related_store' => $this->getTable('sportpat_home_banner_banner_store')
                ],
                'related_store.banner_id = main_table.banner_id'
            );
            $this->getSelect()->where('related_store.store_id IN (?)', [$storeId, 0]);
            $this->joinFields['store'] = true;
        }
        return $this;
    }

    /**
     * Join store relation table
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->getSelect()->join(
            ['store_table' => $this->getTable('sportpat_home_banner_banner_store')],
            'main_table.banner_id = store_table.banner_id',
            []
        )->group(
            'main_table.banner_id'
        );
        parent::_renderFiltersBefore();
    }
}
