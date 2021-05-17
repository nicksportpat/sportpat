<?php
namespace Sportpat\OrderSync\Observer;
use Magento\Framework\Event\ObserverInterface;
use Sportpat\OrderSync\Api\Data\SyncedorderInterface;
use Sportpat\OrderSync\Api\SyncedorderRepositoryInterface;
use Sportpat\OrderSync\Api\Data\SyncedorderInterfaceFactory;

class SyncToLs implements ObserverInterface {

    protected $syncorderrepo;
    protected $syncedorder;
    protected $factory;


    public function __construct(SyncedorderRepositoryInterface $syncorderrepo,
SyncedorderInterface $syncedorder,
                                SyncedorderInterfaceFactory $syncedorderFactory) {

        $this->syncorderrepo = $syncorderrepo;
        $this->syncedorder = $syncedorder;
        $this->factory = $syncedorderFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $this->insertOrderInTable($order);

    }

    public function insertOrderInTable($order) {


        /** @var SyncedorderInterface $syncedorder */
        $so = $this->factory->create();
        $so->setMagentoOrderid($order->getIncrementId());
        $so->setStatus(1);
        $so->save();



    }

}