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

class Import extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Bss\ReviewsImport\Model\ResourceModel\Import
     */
    protected $importModel;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $varDirectory;

    /**
     * @var \Bss\ReviewsImport\Model\ResourceModel\Review
     */
    protected $reviewEntity;

    /**
     * Import constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Bss\ReviewsImport\Model\ResourceModel\Import $importModel
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Bss\ReviewsImport\Model\ResourceModel\Review $reviewEntity
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Bss\ReviewsImport\Model\ResourceModel\Import $importModel,
        \Magento\Framework\App\Request\Http $request,
        \Bss\ReviewsImport\Model\ResourceModel\Review $reviewEntity
    ) {
        parent::__construct($context);
        $this->importModel = $importModel;
        $this->request = $request;
        $this->reviewEntity = $reviewEntity;
    }

    /**
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $filepath="import/reviews/".$this->request->getFiles('file')['name'];
        try {
            $this->importModel->setFilePath($filepath);
            $this->importModel->importFromCsvFile();
            $this->messageManager->addSuccess(__('Inserted Row(s): '. $this->reviewEntity->getInsertedRows()));
            if ($this->reviewEntity->getExistingRows()>0) {
                $this->messageManager->addError(__('Existing Row(s): ' . $this->reviewEntity->getExistingRows()));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $this->resultRedirectFactory->create()->setPath(
            '*/*/index',
            ['_secure'=>$this->getRequest()->isSecure()]
        );
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Bss_ReviewsImport::import_product_review');
    }
}
