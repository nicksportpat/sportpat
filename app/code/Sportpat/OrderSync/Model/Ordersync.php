<?php
namespace Sportpat\OrderSync\Model;



class Ordersync {

    protected $_helper;
    protected $_obj;



    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager) {
        $this->_obj = $objectManager;
    }


    public function syncOrders($syncType, $output) {

     /*  $helper = $this->_obj->get("Sportpat\OrderSync\Helper\SyncData");
       $helper->getPendingOrdersToSync();
*/
echo "run";


    }

}