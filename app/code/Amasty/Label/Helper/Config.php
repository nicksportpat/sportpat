<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 * @package Amasty\Label\Helper
 */
class Config extends AbstractHelper
{
    const AMASTY_LABEL_MEDIA_PATH = 'amasty/amlabel/';
    const AMASTY_LABEL_CONFIG_PATH = 'amasty_label/';
    const MAX_LABELS = 999;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    private $ioFile;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Amasty\Label\Model\ResourceModel\Labels\CollectionFactory
     */
    private $labelCollectionFactory;

    /**
     * @var \Amasty\Label\Model\Repository\LabelsRepository
     */
    private $labelsRepository;

    /**
     * Image constructor.
     * @param Context $context
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Amasty\Label\Model\ResourceModel\Labels\CollectionFactory $labelCollectionFactory,
        \Amasty\Label\Model\Repository\LabelsRepository $labelsRepository
    ) {
        parent::__construct($context);
        $this->filesystem = $filesystem;
        $this->ioFile = $ioFile;
        $this->storeManager = $storeManager;
        $this->labelCollectionFactory = $labelCollectionFactory;
        $this->labelsRepository = $labelsRepository;
    }

    /**
     * @param string $path
     * @return mixed
     */
    public function getModuleConfig($path)
    {
        return $this->scopeConfig->getValue(
            self::AMASTY_LABEL_CONFIG_PATH . $path,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * return url with magento path
     * @param string $name
     * @return string
     */
    public function getImageUrl($name)
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            self::AMASTY_LABEL_MEDIA_PATH
        );

        if ($name != "" && $this->ioFile->fileExists($path . $name)) {
            $path = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            return $path . self::AMASTY_LABEL_MEDIA_PATH . $name;
        }

        return '';
    }

    /**
     * @param  string $name
     * @return string
     */
    public function getImagePath($name)
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            self::AMASTY_LABEL_MEDIA_PATH
        );

        if ($this->ioFile->fileExists($path . $name) && $name != "") {
            return $path . $name;
        }

        return '';
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isLabelExist($id)
    {
        $label = $this->labelCollectionFactory->create()
            ->addFieldToFilter('stores', $this->storeManager->getStore()->getId())
            ->addFieldToFilter('label_id', $id);

        return (bool)$label->getSize();
    }

    /**
     * @param int $id
     * @param int $status
     */
    public function changeStatus($id, $status)
    {
        if ($this->isLabelExist($id)) {
            $label = $this->labelsRepository->getById($id);
            $label->setStatus($status);
            $label->save();
        }
    }

    /**
     * @return int
     */
    public function getMaxLabels()
    {
        $maxLabels = $this->getModuleConfig('display/max_labels');
        if ($maxLabels === null) {
            $maxLabels = self::MAX_LABELS;
        }
        return (int)$maxLabels;
    }
}
