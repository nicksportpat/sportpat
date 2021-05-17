<?php
namespace Sportpat\OrderSync\Model\ResourceModel\Syncedorder;

use Sportpat\OrderSync\Model\Syncedorder;
use Sportpat\OrderSync\Model\ResourceModel\AbstractCollection;

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
            Syncedorder::class,
            \Sportpat\OrderSync\Model\ResourceModel\Syncedorder::class
        );
    }
}
