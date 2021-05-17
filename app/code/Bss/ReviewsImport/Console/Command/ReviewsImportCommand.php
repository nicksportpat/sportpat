<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ReviewsImport
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ReviewsImport\Console\Command;

use Magento\Framework\App\State as AppState;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ReviewsImportCommand extends \Symfony\Component\Console\Command\Command
{
    const INPUT_KEY_FILE = 'file';

    /**
     * @var \Bss\ReviewsImport\Model\ResourceModel\Import
     */
    protected $importModel;

    /**
     * @var AppState
     */
    protected $appState;

    /**
     * ReviewsImportCommand constructor.
     * @param \Bss\ReviewsImport\Model\ResourceModel\Import $importModel
     * @param AppState $appState
     */
    public function __construct(
        \Bss\ReviewsImport\Model\ResourceModel\Import $importModel,
        AppState $appState
    ) {
        $this->importModel = $importModel;
        $this->appState = $appState;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->setName('bss:reviews:import')
            ->setDescription('Import Reviews with csv.');

        $this->addArgument(
            self::INPUT_KEY_FILE,
            InputArgument::REQUIRED,
            'Path of the file. For example, var/import/review/espa.csv'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode('adminhtml');

        $output->writeln('Reading File...');
        $filePath = $input->getArgument(self::INPUT_KEY_FILE);

        $this->importModel->setFilePath($filePath);
        $output->writeln($this->importModel->getFilePath());
        $this->importModel->processImport($output);

        $output->writeln('Import Completed.');
    }
}
