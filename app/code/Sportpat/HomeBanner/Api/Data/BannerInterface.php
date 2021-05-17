<?php
namespace Sportpat\HomeBanner\Api\Data;

/**
 * @api
 */
interface BannerInterface
{
    const BANNER_ID = 'banner_id';
    const BANNER_NAME = 'banner_name';
    const BANNER_IMAGE = 'banner_image';
    const BANNER_LINK = 'banner_link';
    const BANNER_WIDTH = 'banner_width';
    const BANNER_HEIGHT = 'banner_height';
    const BANNER_SIZE = 'banner_size';
    const BANNER_ORDER = 'banner_order';
    const BANNER_SHOWFROMDATE = 'banner_showfromdate';
    const BANNER_SHOWTODATE = 'banner_showtodate';
    /**
     * @var string
     */
    const STORE_ID = 'store_id';
    /**
     * @var string
     */
    const IS_ACTIVE = 'is_active';
    /**
     * @var int
     */
    const STATUS_ENABLED = 1;
    /**
     * @var int
     */
    const STATUS_DISABLED = 2;
    /**
     * @param int $id
     * @return BannerInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return BannerInterface
     */
    public function setBannerId($id);

    /**
     * @return int
     */
    public function getBannerId();

    /**
     * @param string $bannerName
     * @return BannerInterface
     */
    public function setBannerName($bannerName);

    /**
     * @return string
     */
    public function getBannerName();
    /**
     * @param string $bannerImage
     * @return BannerInterface
     */
    public function setBannerImage($bannerImage);

    /**
     * @return string
     */
    public function getBannerImage();
    /**
     * @param string $bannerLink
     * @return BannerInterface
     */
    public function setBannerLink($bannerLink);

    /**
     * @return string
     */
    public function getBannerLink();
    /**
     * @param string $bannerWidth
     * @return BannerInterface
     */
    public function setBannerWidth($bannerWidth);

    /**
     * @return string
     */
    public function getBannerWidth();
    /**
     * @param string $bannerHeight
     * @return BannerInterface
     */
    public function setBannerHeight($bannerHeight);

    /**
     * @return string
     */
    public function getBannerHeight();
    /**
     * @param int $bannerSize
     * @return BannerInterface
     */
    public function setBannerSize($bannerSize);

    /**
     * @return int
     */
    public function getBannerSize();
    /**
     * @param int $bannerOrder
     * @return BannerInterface
     */
    public function setBannerOrder($bannerOrder);

    /**
     * @return int
     */
    public function getBannerOrder();
    /**
     * @param string $bannerShowfromdate
     * @return BannerInterface
     */
    public function setBannerShowfromdate($bannerShowfromdate);

    /**
     * @return string
     */
    public function getBannerShowfromdate();
    /**
     * @param string $bannerShowtodate
     * @return BannerInterface
     */
    public function setBannerShowtodate($bannerShowtodate);

    /**
     * @return string
     */
    public function getBannerShowtodate();

    /**
     * @return bool|string
     * @throws LocalizedException
     */
    public function getBannerImageUrl();

    /**
     * @param int[] $store
     * @return BannerInterface
     */
    public function setStoreId(array $store);

    /**
     * @return int[]
     */
    public function getStoreId();
    /**
     * @param int $isActive
     * @return BannerInterface
     */
    public function setIsActive($isActive);

    /**
     * @return int
     */
    public function getIsActive();
}
