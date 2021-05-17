<?php
namespace Sportpat\Tabcontent\Model\ResourceModel\Tabcontent;

use Sportpat\Tabcontent\Model\Tabcontent;
use Sportpat\Tabcontent\Model\ResourceModel\AbstractCollection;

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
            Tabcontent::class,
            \Sportpat\Tabcontent\Model\ResourceModel\Tabcontent::class
        );
        $this->_map['fields']['store_id'] = 'store_table.store_id';
        $this->_map['fields']['tabcontent_id'] = 'main_table.tabcontent_id';
    }

    /**
     * after collection load
     */
    protected function _afterLoad()
    {
        $fields = [
            'for_gender'
        ];
        /** @var Tabcontent $item */
        foreach ($this as $item) {
            foreach ($fields as $field) {
                if (!is_array($item->getData($field))) {
                    $item->setData($field, explode(',', $item->getData($field)));
                }
            }
        }
        $ids = [];
        foreach ($this as $item) {
            $ids[] = $item->getId();
        }
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(
                ['store_table' => $this->getTable('sportpat_tabcontent_tabcontent_store')]
            )->where('store_table.tabcontent_id IN (?)', $ids);
            $result = $connection->fetchAll($select);
            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData['tabcontent_id']][] = $storeData['store_id'];
                }
                foreach ($this as $item) {
                    $linkedId = $item->getData('tabcontent_id');
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
                    'related_store' => $this->getTable('sportpat_tabcontent_tabcontent_store')
                ],
                'related_store.tabcontent_id = main_table.tabcontent_id'
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
            ['store_table' => $this->getTable('sportpat_tabcontent_tabcontent_store')],
            'main_table.tabcontent_id = store_table.tabcontent_id',
            []
        )->group(
            'main_table.tabcontent_id'
        );
        parent::_renderFiltersBefore();
    }
}
