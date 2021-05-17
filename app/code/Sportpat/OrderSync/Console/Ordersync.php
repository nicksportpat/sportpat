<?php
namespace Sportpat\OrderSync\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Sportpat\OrderSync\Helper\SyncData;


class Ordersync extends Command {

    protected $_helper;
    protected $_state;

    public function __construct(\Sportpat\OrderSync\Helper\SyncData $helper) {


        parent::__construct();

        $this->_helper = $helper;


    }
    protected function configure(){
        $this->setName('sportpat:ordersync');
        $this->setDescription('Sportpat main sync command line');
        $this->addArgument('syncType', InputArgument::REQUIRED, 'Sync Type (simple, matrix, image, all, cron)');




    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
   //    $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND); // or \Magento\Framework\App\Area::AREA_ADMINHTML, depending on your needs


        try {
            $syncType = $input->getArgument('syncType');

            $this->_helper->getPendingOrdersToSync();


        } catch(\Exception $e){
            $output->writeln($e->getMessage());
        }

    }

}