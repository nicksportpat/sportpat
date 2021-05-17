<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Controller\Adminhtml;

use Amasty\Label\Api\LabelRepositoryInterface;

/**
 * Items controller
 */
abstract class Labels extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * File system
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * File Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * File check
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $ioFile;

    /**
     * @var \Amasty\Label\Helper\Shape
     */
    protected $shapeHelper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Amasty\Base\Model\Serializer
     */
    protected $serializer;

    /**
     * @var \Amasty\Label\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Amasty\Label\Model\Indexer\LabelIndexer
     */
    protected $labelIndexer;

    /**
     * @var \Amasty\Label\Helper\Config
     */
    protected $config;

    /**
     * @var LabelRepositoryInterface
     */
    protected $labelRepository;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Amasty\Label\Helper\Shape $shapeHelper,
        \Psr\Log\LoggerInterface $logger,
        \Amasty\Base\Model\Serializer $serializer,
        \Amasty\Label\Model\RuleFactory $ruleFactory,
        \Amasty\Label\Model\Indexer\LabelIndexer $labelIndexer,
        \Amasty\Label\Helper\Config $config,
        LabelRepositoryInterface $labelRepository,
        \Magento\Framework\Escaper $escaper
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->filesystem = $filesystem;
        $this->ioFile = $ioFile;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->shapeHelper = $shapeHelper;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->ruleFactory = $ruleFactory;
        $this->labelIndexer = $labelIndexer;
        $this->config = $config;
        $this->labelRepository = $labelRepository;
        $this->escaper = $escaper;
    }

    /**
     * Initiate action
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Amasty_Label::labels')->_addBreadcrumb(__('Product Labels'), __('Product Labels'));
        return $this;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_Label::label');
    }
}
