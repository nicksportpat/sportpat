<?php
namespace Sportpat\Tabcontent\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Tabcontent extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sportpat_tabcontent_tabcontent', 'tabcontent_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $tabcontentId
     * @return array
     */
    public function lookupStoreIds($tabcontentId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['store_table' => $this->getTable('sportpat_tabcontent_tabcontent_store')], 'store_id')
            ->join(
                ['main_table' => $this->getMainTable()],
                'store_table.tabcontent_id = main_table.tabcontent_id',
                []
            )->where('main_table.tabcontent_id = :tabcontent_id');
        return $connection->fetchCol($select, ['tabcontent_id' => (int)$tabcontentId]);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel | \Sportpat\Tabcontent\Model\Tabcontent $object
     * @return $this | AbstractDb
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStoreId();
        $table  = $this->getTable('sportpat_tabcontent_tabcontent_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                'tabcontent_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            ];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'tabcontent_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                ];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel | \Sportpat\Tabcontent\Model\Tabcontent $object
     * @return $this|AbstractModel
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterLoad($object);
        $object->setStoreId($this->lookupStoreIds($object->getId()));
        return $this;
    }
}
