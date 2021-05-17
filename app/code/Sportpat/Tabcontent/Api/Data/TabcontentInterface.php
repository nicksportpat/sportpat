<?php
namespace Sportpat\Tabcontent\Api\Data;

/**
 * @api
 */
interface TabcontentInterface
{
    const TABCONTENT_ID = 'tabcontent_id';
    const TITLE = 'title';
    const TAB_CONTENTTYPE = 'tab_contenttype';
    const CONTENT_HTML = 'content_html';
    const IMAGE = 'image';
    const FOR_BRAND = 'for_brand';
    const FOR_CATEGORY = 'for_category';
    const USE_FOR_SKUS = 'use_for_skus';
    const FOR_GENDER = 'for_gender';
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
     * @return TabcontentInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return TabcontentInterface
     */
    public function setTabcontentId($id);

    /**
     * @return int
     */
    public function getTabcontentId();

    /**
     * @param string $title
     * @return TabcontentInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();
    /**
     * @param int $tabContenttype
     * @return TabcontentInterface
     */
    public function setTabContenttype($tabContenttype);

    /**
     * @return int
     */
    public function getTabContenttype();
    /**
     * @param string $contentHtml
     * @return TabcontentInterface
     */
    public function setContentHtml($contentHtml);

    /**
     * @return string
     */
    public function getContentHtml();
    /**
     * @param string $image
     * @return TabcontentInterface
     */
    public function setImage($image);

    /**
     * @return string
     */
    public function getImage();
    /**
     * @param int $forBrand
     * @return TabcontentInterface
     */
    public function setForBrand($forBrand);

    /**
     * @return int
     */
    public function getForBrand();
    /**
     * @param int $forCategory
     * @return TabcontentInterface
     */
    public function setForCategory($forCategory);

    /**
     * @return int
     */
    public function getForCategory();
    /**
     * @param string $useForSkus
     * @return TabcontentInterface
     */
    public function setUseForSkus($useForSkus);

    /**
     * @return string
     */
    public function getUseForSkus();
    /**
     * @param string $forGender
     * @return TabcontentInterface
     */
    public function setForGender($forGender);

    /**
     * @return string
     */
    public function getForGender();

    /**
     * @return bool|string
     * @throws LocalizedException
     */
    public function getImageUrl();

    /**
     * @param int[] $store
     * @return TabcontentInterface
     */
    public function setStoreId(array $store);

    /**
     * @return int[]
     */
    public function getStoreId();
    /**
     * @param int $isActive
     * @return TabcontentInterface
     */
    public function setIsActive($isActive);

    /**
     * @return int
     */
    public function getIsActive();
}
