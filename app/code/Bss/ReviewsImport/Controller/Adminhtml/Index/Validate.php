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
namespace Bss\ReviewsImport\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;

class Validate extends Action
{
    /**
     * @var \Bss\ReviewsImport\Model\ResourceModel\Import
     */
    protected $importModel;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $varDirectory;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Bss\ReviewsImport\Model\ResourceModel\Review
     */
    protected $reviewEntity;

    /**
     * @var \Magento\Framework\File\Size
     */
    protected $fileSize;

    /**
     * Validate constructor.
     * @param Action\Context $context
     * @param \Bss\ReviewsImport\Model\ResourceModel\Import $importModel
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Bss\ReviewsImport\Model\ResourceModel\Review $reviewEntity
     * @param \Magento\Framework\File\Size $fileSize
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        \Bss\ReviewsImport\Model\ResourceModel\Import $importModel,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Bss\ReviewsImport\Model\ResourceModel\Review $reviewEntity,
        \Magento\Framework\File\Size $fileSize,
        JsonFactory $resultJsonFactory
    ) {
        $this->importModel = $importModel;
        $this->filesystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->reviewEntity = $reviewEntity;
        $this->fileSize = $fileSize;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function execute()
    {
        $file = $this->getRequest()->getFiles('file');
        $this->varDirectory = $this->filesystem
            ->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
        $filepath = "import/reviews/" . $file['name'];
        $size = $file['size'];
        $resultJson = $this->resultJsonFactory->create();

        if (($size==0) || ($size > $this->fileSize->getMaxFileSize())) {
            $msesages['error'] = [$this->getMaxUploadSizeMessage()];
            $resultJson->setData($msesages);
            return $resultJson;
        }

        try {
            $target = $this->varDirectory->getAbsolutePath('import/reviews');
            $uploader = $this->fileUploaderFactory->create(['fileId' => 'file']);
            $uploader->setAllowedExtensions(['csv']);
            $uploader->setAllowRenameFiles(false);
            $result = $uploader->save($target);


            $this->importModel->setFilePath($filepath);
            $missingColumnNames = rtrim($this->importModel->missingColumnsNames(), ', ');

            if (empty($missingColumnNames)) {
                $this->importModel->validateCsvFile();

                $errorMessages = [];
                $errorMessages[] = "Invalid Row(s): " . $this->reviewEntity->getErrorRows();
                foreach ($this->getErrorMessages() as $code => $message) {
                    $rowNums = $this->reviewEntity->getErrorRows($code);
                    if (!empty($rowNums)) {
                        $errorMessages[] = $message . $rowNums;
                    }
                }

                if ($this->reviewEntity->getInvalidRows() > 0) {
                    $msesages['error'] = $errorMessages;
                } else {
                    if ($result['file']) {
                        $msesages = [
                            'success' => [__("File is valid. Please click import button"),
                                __('File has been successfully uploaded in var/import/reviews')]
                        ];
                    }
                }
            } else {
                $msesages['error'] = [__('Missing column(s): %1', $missingColumnNames)];
            }

        } catch (\Exception $e) {
            $msesages['error'] = [$e->getMessage()];
        }
        $resultJson->setData($msesages);
        return $resultJson;
    }

    /**
     * @return string
     */
    protected function getMaxUploadSizeMessage()
    {
        $maxImageSize = $this->fileSize->getMaxFileSizeInMb();
        if ($maxImageSize) {
            $message = __('Make sure your file isn\'t more than %1M.', $maxImageSize);
        } else {
            $message = __('We can\'t provide the upload settings right now.');
        }
        return $message;
    }

    /**
     * @return array
     */
    protected function getErrorMessages()
    {
        return [
            "emptyRequiredDataRows" => "Empty required data in row(s): ",
            "invalidDateRows" => "Invalid Date in row(s): ",
            "invalidStatusRows" => "Invalid Status in rows(s): ",
            "invalidRatingRows" => "Invalid Rating in row(s): ",
            "invalidReviewIdRows" => "Invalid Review Id in row(s): ",
            "invalidProductSkuRows" => "Invalid Product Sku in row(s): "
        ];
    }
}
