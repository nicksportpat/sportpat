<?php
namespace Sportpat\HomeBanner\Controller\Adminhtml\Banner;

use Sportpat\HomeBanner\Api\BannerRepositoryInterface;
use Sportpat\HomeBanner\Api\Data\BannerInterface;
use Sportpat\HomeBanner\Model\ResourceModel\Banner as BannerResourceModel;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Sportpat\HomeBanner\Model\DateFilter;

/**
 * Class InlineEdit
 */
class InlineEdit extends Action
{
    /**
     * Banner repository
     * @var BannerRepositoryInterface
     */
    protected $bannerRepository;
    /**
     * Data object processor
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * Data object helper
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * JSON Factory
     * @var JsonFactory
     */
    protected $jsonFactory;
    /**
     * Date inputs filter
     * @var DateFilter
     */
    protected $dateFilter;
    /**
     * Banner resource model
     * @var BannerResourceModel
     */
    protected $bannerResourceModel;

    /**
     * constructor
     * @param Context $context
     * @param BannerRepositoryInterface $bannerRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param DataObjectHelper $dataObjectHelper
     * @param JsonFactory $jsonFactory
     * @param BannerResourceModel $bannerResourceModel
     */
    public function __construct(
        Context $context,
        BannerRepositoryInterface $bannerRepository,
        DataObjectProcessor $dataObjectProcessor,
        DataObjectHelper $dataObjectHelper,
        JsonFactory $jsonFactory,
        DateFilter $dateFilter,
        BannerResourceModel $bannerResourceModel
    ) {
        $this->bannerRepository = $bannerRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->jsonFactory = $jsonFactory;
        $this->dateFilter = $dateFilter;
        $this->bannerResourceModel = $bannerResourceModel;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $bannerId) {
            /** @var \Sportpat\HomeBanner\Model\Banner|\Sportpat\HomeBanner\Api\Data\BannerInterface $banner */
            try {
                $banner = $this->bannerRepository->get((int)$bannerId);
                $bannerData = $postItems[$bannerId];
                $bannerData = $this->dateFilter->filterDates(
                    $bannerData,
                    [
                        'banner_showfromdate',
                        'banner_showtodate'
                    ]
                );
                $this->dataObjectHelper->populateWithArray($banner, $bannerData, BannerInterface::class);
                $this->bannerResourceModel->saveAttribute($banner, array_keys($bannerData));
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithBannerId($banner, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithBannerId($banner, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithBannerId(
                    $banner,
                    __('Something went wrong while saving the Banner.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Banner id to error message
     *
     * @param \Sportpat\HomeBanner\Api\Data\BannerInterface $banner
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithBannerId(BannerInterface $banner, $errorText)
    {
        return '[Banner ID: ' . $banner->getId() . '] ' . $errorText;
    }
}
