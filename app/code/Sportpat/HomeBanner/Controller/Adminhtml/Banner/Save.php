<?php
namespace Sportpat\HomeBanner\Controller\Adminhtml\Banner;

use Sportpat\HomeBanner\Api\BannerRepositoryInterface;
use Sportpat\HomeBanner\Api\Data\BannerInterface;
use Sportpat\HomeBanner\Api\Data\BannerInterfaceFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;
use Sportpat\HomeBanner\Model\UploaderPool;
use Sportpat\HomeBanner\Model\DateFilter;

/**
 * Class Save
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action
{
    /**
     * Banner factory
     * @var BannerInterfaceFactory
     */
    protected $bannerFactory;
    /**
     * Data Object Processor
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * Data Object Helper
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * Data Persistor
     * @var DataPersistorInterface
     */
    protected $dataPersistor;
    /**
     * Uploader pool
     * @var UploaderPool
     */
    protected $uploaderPool;
    /**
     * Date inputs filter
     * @var DateFilter
     */
    protected $dateFilter;
    /**
     * Core registry
     * @var Registry
     */
    protected $registry;
    /**
     * Banner repository
     * @var BannerRepositoryInterface
     */
    protected $bannerRepository;

    /**
     * Save constructor.
     * @param Context $context
     * @param BannerInterfaceFactory $bannerFactory
     * @param BannerRepositoryInterface $bannerRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param DataPersistorInterface $dataPersistor
     * @param UploaderPool $uploaderPool
     * @param DateFilter $dateFilter
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        BannerInterfaceFactory $bannerFactory,
        BannerRepositoryInterface $bannerRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        DataPersistorInterface $dataPersistor,
        UploaderPool $uploaderPool,
        DateFilter $dateFilter,
        Registry $registry
    ) {
        $this->bannerFactory = $bannerFactory;
        $this->bannerRepository = $bannerRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPersistor = $dataPersistor;
        $this->registry = $registry;
        $this->uploaderPool = $uploaderPool;
        $this->dateFilter = $dateFilter;
        parent::__construct($context);
    }

    /**
     * run the action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var BannerInterface $banner */
        $banner = null;
        $postData = $this->getRequest()->getPostValue();
        $data = $postData;
        $data = $this->dateFilter->filterDates(
            $data,
            [
                'banner_showfromdate',
                'banner_showtodate'
            ]
        );
        $id = !empty($data['banner_id']) ? $data['banner_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $banner = $this->bannerRepository->get((int)$id);
            } else {
                unset($data['banner_id']);
                $banner = $this->bannerFactory->create();
            }
            $bannerImage = $this->uploaderPool->getUploader('image')->uploadFileAndGetName('banner_image', $data);
            $data['banner_image'] = $bannerImage;
            $this->dataObjectHelper->populateWithArray($banner, $data, BannerInterface::class);
            $this->bannerRepository->save($banner);
            $this->messageManager->addSuccessMessage(__('You saved the Banner'));
            $this->dataPersistor->clear('sportpat_home_banner_banner');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('*/*/edit', ['banner_id' => $banner->getId()]);
            } else {
                $resultRedirect->setPath('*/*');
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('sportpat_home_banner_banner', $postData);
            $resultRedirect->setPath('*/*/edit', ['banner_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Banner'));
            $this->dataPersistor->set('sportpat\home_banner_banner', $postData);
            $resultRedirect->setPath('*/*/edit', ['banner_id' => $id]);
        }
        return $resultRedirect;
    }
}
