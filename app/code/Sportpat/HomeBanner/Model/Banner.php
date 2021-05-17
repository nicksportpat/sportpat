<?php
namespace Sportpat\HomeBanner\Model;

use Sportpat\HomeBanner\Api\Data\BannerInterface;
use Magento\Framework\Model\AbstractModel;
use Sportpat\HomeBanner\Model\ResourceModel\Banner as BannerResourceModel;
use Magento\Framework\Data\Collection\AbstractDb as DbCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Option\ArrayInterface;

/**
 * @method \Sportpat\HomeBanner\Model\ResourceModel\Banner _getResource()
 * @method \Sportpat\HomeBanner\Model\ResourceModel\Banner getResource()
 */
class Banner extends AbstractModel implements BannerInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'sportpat_homebanner_banner';
    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sportpat_homebanner_banner';
    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'banner';
    /**
     * Uploader pool
     *
     * @var UploaderPool
     */
    protected $uploaderPool;
    /**
     * @var ArrayInterface[]
     */
    protected $optionProviders;
    /**
     * constructor
     * @param Context $context
     * @param Registry $registry
     * @param UploaderPool $uploaderPool
     * @param array $optionProviders
     * @param AbstractResource $resource
     * @param DbCollection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        UploaderPool $uploaderPool,
        array $optionProviders = [],
        AbstractResource $resource = null,
        DbCollection $resourceCollection = null,
        array $data = []
    ) {
        $this->uploaderPool = $uploaderPool;
        $this->optionProviders = $optionProviders;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(BannerResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Page id
     *
     * @return array
     */
    public function getBannerId()
    {
        return $this->getData(BannerInterface::BANNER_ID);
    }

    /**
     * set Banner id
     *
     * @param  int $bannerId
     * @return BannerInterface
     */
    public function setBannerId($bannerId)
    {
        return $this->setData(BannerInterface::BANNER_ID, $bannerId);
    }

    /**
     * @param string $bannerName
     * @return BannerInterface
     */
    public function setBannerName($bannerName)
    {
        return $this->setData(BannerInterface::BANNER_NAME, $bannerName);
    }

    /**
     * @return string
     */
    public function getBannerName()
    {
        return $this->getData(BannerInterface::BANNER_NAME);
    }

    /**
     * @param string $bannerImage
     * @return BannerInterface
     */
    public function setBannerImage($bannerImage)
    {
        return $this->setData(BannerInterface::BANNER_IMAGE, $bannerImage);
    }

    /**
     * @return string
     */
    public function getBannerImage()
    {
        return $this->getData(BannerInterface::BANNER_IMAGE);
    }

    /**
     * @param string $bannerLink
     * @return BannerInterface
     */
    public function setBannerLink($bannerLink)
    {
        return $this->setData(BannerInterface::BANNER_LINK, $bannerLink);
    }

    /**
     * @return string
     */
    public function getBannerLink()
    {
        return $this->getData(BannerInterface::BANNER_LINK);
    }

    /**
     * @param string $bannerWidth
     * @return BannerInterface
     */
    public function setBannerWidth($bannerWidth)
    {
        return $this->setData(BannerInterface::BANNER_WIDTH, $bannerWidth);
    }

    /**
     * @return string
     */
    public function getBannerWidth()
    {
        return $this->getData(BannerInterface::BANNER_WIDTH);
    }

    /**
     * @param string $bannerHeight
     * @return BannerInterface
     */
    public function setBannerHeight($bannerHeight)
    {
        return $this->setData(BannerInterface::BANNER_HEIGHT, $bannerHeight);
    }

    /**
     * @return string
     */
    public function getBannerHeight()
    {
        return $this->getData(BannerInterface::BANNER_HEIGHT);
    }

    /**
     * @param int $bannerSize
     * @return BannerInterface
     */
    public function setBannerSize($bannerSize)
    {
        return $this->setData(BannerInterface::BANNER_SIZE, $bannerSize);
    }

    /**
     * @return int
     */
    public function getBannerSize()
    {
        return $this->getData(BannerInterface::BANNER_SIZE);
    }

    /**
     * @param int $bannerOrder
     * @return BannerInterface
     */
    public function setBannerOrder($bannerOrder)
    {
        return $this->setData(BannerInterface::BANNER_ORDER, $bannerOrder);
    }

    /**
     * @return int
     */
    public function getBannerOrder()
    {
        return $this->getData(BannerInterface::BANNER_ORDER);
    }

    /**
     * @param string $bannerShowfromdate
     * @return BannerInterface
     */
    public function setBannerShowfromdate($bannerShowfromdate)
    {
        return $this->setData(BannerInterface::BANNER_SHOWFROMDATE, $bannerShowfromdate);
    }

    /**
     * @return string
     */
    public function getBannerShowfromdate()
    {
        return $this->getData(BannerInterface::BANNER_SHOWFROMDATE);
    }

    /**
     * @param string $bannerShowtodate
     * @return BannerInterface
     */
    public function setBannerShowtodate($bannerShowtodate)
    {
        return $this->setData(BannerInterface::BANNER_SHOWTODATE, $bannerShowtodate);
    }

    /**
     * @return string
     */
    public function getBannerShowtodate()
    {
        return $this->getData(BannerInterface::BANNER_SHOWTODATE);
    }
    /**
     * @param array $storeId
     * @return BannerInterface
     */
    public function setStoreId(array $storeId)
    {
        return $this->setData(BannerInterface::STORE_ID, $storeId);
    }

    /**
    * @return int[]
    */
    public function getStoreId()
    {
        return $this->getData(BannerInterface::STORE_ID);
    }

    /**
     * @param int $isActive
     * @return BannerInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(BannerInterface::IS_ACTIVE, $isActive);
    }

    /**
     * @return int
     */
    public function getIsActive()
    {
        return $this->getData(BannerInterface::IS_ACTIVE);
    }

    /**
     * @return bool|string
     * @throws LocalizedException
     */
    public function getBannerImageUrl()
    {
        $url = false;
        $bannerImage = $this->getBannerImage();
        if ($bannerImage) {
            if (is_string($bannerImage)) {
                $uploader = $this->uploaderPool->getUploader('image');
                $url = $uploader->getBaseUrl() . $uploader->getBasePath() . $bannerImage;
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the Banner image url.')
                );
            }
        }
        return $url;
    }
    /**
     * @param $attribute
     * @return string
     */
    public function getAttributeText($attribute)
    {
        if (!isset($this->optionProviders[$attribute])) {
            return '';
        }
        if (!($this->optionProviders[$attribute] instanceof ArrayInterface)) {
            return '';
        }
        $value = $this->getData($attribute);
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        $keyValuePair = array_filter(
            $this->optionProviders[$attribute]->toOptionArray(),
            function ($item) use ($value) {
                return in_array($item['value'], $value);
            }
        );
        return implode(
            ', ',
            array_map(
                function ($item) {
                    return $item['label'];
                },
                $keyValuePair
            )
        );
    }
}
