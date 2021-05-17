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
namespace Bss\ReviewsImport\Model\ResourceModel;

use Magento\Framework\App\Filesystem\DirectoryList;

class Import
{
    /**
     * @var \Magento\ImportExport\Model\Import\Source\CsvFactory
     */
    protected $sourceCsvFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var Review
     */
    protected $reviewEntity;

    /**
     * Import constructor.
     * @param \Magento\ImportExport\Model\Import\Source\CsvFactory $sourceCsvFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param Review $reviewEntity
     */
    public function __construct(
        \Magento\ImportExport\Model\Import\Source\CsvFactory $sourceCsvFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Bss\ReviewsImport\Model\ResourceModel\Review $reviewEntity
    ) {
        $this->sourceCsvFactory = $sourceCsvFactory;
        $this->filesystem = $filesystem;
        $this->reviewEntity = $reviewEntity;
    }

    /**
     * @param $output
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function processImport($output)
    {
        $sourceCsv = $this->createSourceCsvModel($this->getFilePath());

        $numRow = 0;
        $sourceCsv->rewind();
        while ($sourceCsv->valid()) {
            $numRow++;
            $sourceCsv->next();
        }

        $output->write('Imported 0/' . $numRow . ' (0%)');
        $replaceLength = strlen($numRow) + 7;
        
        $sourceCsv->rewind();
        $this->reviewEntity->init();
        $i = 0;
        while ($sourceCsv->valid()) {
            $this->reviewEntity->processData($sourceCsv->current());
            $i++;
            $sourceCsv->next();

            $output->write("\033[".$replaceLength."D");
            $percent = (int) ($i*100)/$numRow;
            $replaceLength = strlen($i) + strlen($numRow) + strlen((int) $percent) + 5;

            $output->write($i. '/' . $numRow . ' (' . (int) $percent . '%)');
        }

        $output->write("\033[".$replaceLength."D");
        $output->write($numRow. '/' . $numRow . ' (100%)');
        $output->write(PHP_EOL);
        $output->writeln('Inserted Row(s): ' . $this->reviewEntity->getInsertedRows());
        $output->writeln('Existing Row(s): ' . $this->reviewEntity->getExistingRows());
        $output->writeln('Invalid Row(s): ' . $this->reviewEntity->getInvalidRows());
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function importFromCsvFile()
    {
        $sourceCsv = $this->createSourceCsvModel($this->getFilePath());

        $sourceCsv->rewind();
        $this->reviewEntity->init();
        $numRow = 0;
        while ($sourceCsv->valid()) {
            $numRow++;
            $this->reviewEntity->processData($sourceCsv->current(), $numRow);
            $sourceCsv->next();
        }
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function validateCsvFile()
    {
        $sourceCsv = $this->createSourceCsvModel($this->getFilePath());

        $sourceCsv->rewind();
        $this->reviewEntity->init();
        $numRow = 0;
        while ($sourceCsv->valid()) {
            $numRow++;
            $this->reviewEntity->validateBeforeImport($sourceCsv->current(), $numRow);
            $sourceCsv->next();
        }
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function missingColumnsNames()
    {
        $sourceCsv = $this->createSourceCsvModel($this->getFilePath());

        /**
         * Array for format Column Name
         */
        $columnNameKeys = [
            'detail',
            'title',
            'nick',
            'type',
            'review',
            'customer',
            'status',
            'sku',
            'rating',
            'store',
            'date'
        ];

        /**
         * Array for render error message
         */
        $rawColumnNames = [
            'detail' => 'Details',
            'title' => 'Title',
            'nick_name' => 'Nick name',
            'type' => 'Type',
            'review_id' => 'Review ID',
            'customer_id' => 'Customer ID',
            'status' => 'Status',
            'sku' => 'Product SKU',
            'rating_option' => 'Rating Option',
            'store_code' => 'Store View Code',
            'date' => 'Date'
        ];

        $columnNamesWithKey = [];
        $columnNames = $sourceCsv->getColNames();
        $formatedColumnNames = [];

        foreach ($columnNames as $columnName) {
            $key_f = strtolower($columnName);
            foreach ($columnNameKeys as $index => $columnKey) {
                if (strpos($key_f, $columnKey) !== false) {
                    $formatedColumnNames[$index] = Review::VALID_COLUMN_NAMES[$index];
                }
            }
            foreach ($rawColumnNames as $key => $name) {
                $columnNamesWithKey[$key] = $name;
            }
        }

        $missingColumns = array_diff(Review::VALID_COLUMN_NAMES, $formatedColumnNames);
        $missingColumnNames = '';
        if (!empty($missingColumns)) {
            foreach ($missingColumns as $columnName) {
                $missingColumnNames .= $columnNamesWithKey[$columnName] . ', ';
            }
        }
        return $missingColumnNames;
    }

    /**
     * @param $sourceFile
     * @return \Magento\ImportExport\Model\Import\Source\Csv
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function createSourceCsvModel($sourceFile)
    {
        return $this->sourceCsvFactory->create(
            [
                'file' => $sourceFile,
                'directory' => $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)
            ]
        );
    }

    /**
     * @param string $filePath
     * @return $this
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
}
