<?php
namespace Sportpat\HomeBanner\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Banner extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sportpat_home_banner_banner', 'banner_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $bannerId
     * @return array
     */
    public function lookupStoreIds($bannerId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['store_table' => $this->getTable('sportpat_home_banner_banner_store')], 'store_id')
            ->join(
                ['main_table' => $this->getMainTable()],
                'store_table.banner_id = main_table.banner_id',
                []
            )->where('main_table.banner_id = :banner_id');
        return $connection->fetchCol($select, ['banner_id' => (int)$bannerId]);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel | \Sportpat\HomeBanner\Model\Banner $object
     * @return $this | AbstractDb
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStoreId();
        $table  = $this->getTable('sportpat_home_banner_banner_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                'banner_id = ?' => (int) $object->getId(),
                'store_id IN (?)' => $delete
            ];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    'banner_id'  => (int) $object->getId(),
                    'store_id' => (int) $storeId
                ];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel | \Sportpat\HomeBanner\Model\Banner $object
     * @return $this|AbstractModel
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterLoad($object);
        $object->setStoreId($this->lookupStoreIds($object->getId()));
        return $this;
    }
}
