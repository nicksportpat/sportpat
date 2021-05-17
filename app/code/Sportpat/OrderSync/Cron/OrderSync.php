<?php
namespace Sportpat\OrderSync\Cron;

use \Psr\Log\LoggerInterface;
use \Sportpat\OrderSync\Helper\SyncData;


class OrderSync {

    protected $logger;
    protected $_helper;

    public function __construct(LoggerInterface $logger, SyncData $helper) {

        $this->logger = $logger;
        $this->_helper = $helper;


    }

    public function execute() {

        $this->_helper->getPendingOrdersToSync();

    }


}