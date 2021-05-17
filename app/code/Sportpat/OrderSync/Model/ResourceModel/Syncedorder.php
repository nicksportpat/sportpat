<?php
namespace Sportpat\OrderSync\Model\ResourceModel;

class Syncedorder extends AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sportpat_order_sync_syncedorder', 'syncedorder_id');
    }
}
