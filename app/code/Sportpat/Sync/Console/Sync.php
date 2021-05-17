<?php
namespace Sportpat\Sync\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sportpat\Sync\Model\SyncInterface as SyncModel;

class Sync extends Command {


    private $syncInterface;
private $state;

    public function __construct(SyncModel $model,\Magento\Framework\App\State $state) {
        parent::__construct();
        $this->state = $state;

        $this->syncInterface = $model;
    }


    protected function configure(){
        $this->setName('sportpat:sync');
        $this->setDescription('Sportpat main sync command line');
        $this->addArgument('syncType', InputArgument::REQUIRED, 'Sync Type (simple, matrix, image, all, cron)');




    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND); // or \Magento\Framework\App\Area::AREA_ADMINHTML, depending on your needs


        try {
            $syncType = $input->getArgument('syncType');
            $this->syncInterface->syncProducts($syncType, $output);

        } catch(\Exception $e){
            $output->writeln($e->getMessage());
        }

    }

}