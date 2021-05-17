<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Api\Data;

interface LabelInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const LABEL_ID = 'label_id';
    const POS = 'pos';
    const IS_SINGLE = 'is_single';
    const NAME = 'name';
    const STORES = 'stores';
    const PROD_TXT = 'prod_txt';
    const PROD_IMG = 'prod_img';
    const PROD_IMAGE_SIZE = 'prod_image_size';
    const PROD_POS = 'prod_pos';
    const PROD_STYLE = 'prod_style';
    const PROD_TEXT_STYLE = 'prod_text_style';
    const CAT_TXT = 'cat_txt';
    const CAT_IMG = 'cat_img';
    const CAT_POS = 'cat_pos';
    const CAT_STYLE = 'cat_style';
    const CAT_IMAGE_SIZE = 'cat_image_size';
    const CAT_TEXT_STYLE = 'cat_text_style';
    const IS_NEW = 'is_new';
    const IS_SALE = 'is_sale';
    const SPECIAL_PRICE_ONLY = 'special_price_only';
    const STOCK_LESS = 'stock_less';
    const STOCK_MORE = 'stock_more';
    const STOCK_STATUS = 'stock_status';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const DATE_RANGE_ENABLED = 'date_range_enabled';
    const FROM_PRICE = 'from_price';
    const TO_PRICE = 'to_price';
    const BY_PRICE = 'by_price';
    const PRICE_RANGE_ENABLED = 'price_range_enabled';
    const CUSTOMER_GROUP_IDS = 'customer_group_ids';
    const COND_SERIALIZE = 'cond_serialize';
    const CUSTOMER_GROUP_ENABLED = 'customer_group_enabled';
    const USE_FOR_PARENT = 'use_for_parent';
    const STATUS = 'status';
    const PRODUCT_STOCK_ENABLED = 'product_stock_enabled';
    const STOCK_HIGHER = 'stock_higher';
    /**#@-*/

    /**#@+
     * constants for temporary data needed for validate conditions
     */
    const CUSTOMER_GROUP_VALID = 'customer_group_valid';
    const DATE_RANGE_VALID = 'date_range_valid';
    /**#@-*/

    /**
     * @return int
     */
    public function getLabelId();

    /**
     * @param int $labelId
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setLabelId($labelId);

    /**
     * @return int
     */
    public function getPos();

    /**
     * @param int $pos
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setPos($pos);

    /**
     * @return int
     */
    public function getIsSingle();

    /**
     * @param int $isSingle
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setIsSingle($isSingle);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getStores();

    /**
     * @param string $stores
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setStores($stores);

    /**
     * @return string
     */
    public function getProdTxt();

    /**
     * @param string $prodTxt
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setProdTxt($prodTxt);

    /**
     * @return string
     */
    public function getProdImg();

    /**
     * @param string $prodImg
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setProdImg($prodImg);

    /**
     * @return string
     */
    public function getProdImageSize();

    /**
     * @param string $prodImageSize
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setProdImageSize($prodImageSize);

    /**
     * @return int
     */
    public function getProdPos();

    /**
     * @param int $prodPos
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setProdPos($prodPos);

    /**
     * @return string
     */
    public function getProdStyle();

    /**
     * @param string $prodStyle
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setProdStyle($prodStyle);

    /**
     * @return string
     */
    public function getProdTextStyle();

    /**
     * @param string $prodTextStyle
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setProdTextStyle($prodTextStyle);

    /**
     * @return string
     */
    public function getCatTxt();

    /**
     * @param string $catTxt
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setCatTxt($catTxt);

    /**
     * @return string
     */
    public function getCatImg();

    /**
     * @param string $catImg
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setCatImg($catImg);

    /**
     * @return int
     */
    public function getCatPos();

    /**
     * @param int $catPos
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setCatPos($catPos);

    /**
     * @return string
     */
    public function getCatStyle();

    /**
     * @param string $catStyle
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setCatStyle($catStyle);

    /**
     * @return string
     */
    public function getCatImageSize();

    /**
     * @param string $catImageSize
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setCatImageSize($catImageSize);

    /**
     * @return string
     */
    public function getCatTextStyle();

    /**
     * @param string $catTextStyle
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setCatTextStyle($catTextStyle);

    /**
     * @return int
     */
    public function getIsNew();

    /**
     * @param int $isNew
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setIsNew($isNew);

    /**
     * @return int
     */
    public function getIsSale();

    /**
     * @param int $isSale
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setIsSale($isSale);

    /**
     * @return int
     */
    public function getSpecialPriceOnly();

    /**
     * @param int $specialPriceOnly
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setSpecialPriceOnly($specialPriceOnly);

    /**
     * @return int|null
     */
    public function getStockLess();

    /**
     * @param int|null $stockLess
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setStockLess($stockLess);

    /**
     * @return int
     */
    public function getStockMore();

    /**
     * @param int $stockMore
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setStockMore($stockMore);

    /**
     * @return int
     */
    public function getStockStatus();

    /**
     * @param int $stockStatus
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setStockStatus($stockStatus);

    /**
     * @return string|null
     */
    public function getFromDate();

    /**
     * @param string|null $fromDate
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setFromDate($fromDate);

    /**
     * @return string|null
     */
    public function getToDate();

    /**
     * @param string|null $toDate
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setToDate($toDate);

    /**
     * @return int
     */
    public function getDateRangeEnabled();

    /**
     * @param int $dateRangeEnabled
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setDateRangeEnabled($dateRangeEnabled);

    /**
     * @return float
     */
    public function getFromPrice();

    /**
     * @param float $fromPrice
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setFromPrice($fromPrice);

    /**
     * @return float
     */
    public function getToPrice();

    /**
     * @param float $toPrice
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setToPrice($toPrice);

    /**
     * @return int
     */
    public function getByPrice();

    /**
     * @param int $byPrice
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setByPrice($byPrice);

    /**
     * @return int
     */
    public function getPriceRangeEnabled();

    /**
     * @param int $priceRangeEnabled
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setPriceRangeEnabled($priceRangeEnabled);

    /**
     * @return string
     */
    public function getCustomerGroupIds();

    /**
     * @param string $customerGroupIds
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setCustomerGroupIds($customerGroupIds);

    /**
     * @return string
     */
    public function getCondSerialize();

    /**
     * @param string $condSerialize
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setCondSerialize($condSerialize);

    /**
     * @return int
     */
    public function getCustomerGroupEnabled();

    /**
     * @param int $customerGroupEnabled
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setCustomerGroupEnabled($customerGroupEnabled);

    /**
     * @return int
     */
    public function getUseForParent();

    /**
     * @param int $useForParent
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setUseForParent($useForParent);

    /**
     * @return int|null
     */
    public function getStatus();

    /**
     * @param int|null $status
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setStatus($status);

    /**
     * @return int|null
     */
    public function getProductStockEnabled();

    /**
     * @param int|null $productStockEnabled
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setProductStockEnabled($productStockEnabled);

    /**
     * @return int|null
     */
    public function getStockHigher();

    /**
     * @param int|null $stockHigher
     *
     * @return \Amasty\Label\Api\Data\LabelInterface
     */
    public function setStockHigher($stockHigher);
}
