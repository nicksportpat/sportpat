<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model;

use Amasty\Label\Api\Data\LabelInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Labels extends AbstractLabels implements LabelInterface, IdentityInterface
{
    /**
     * @var array
     */
    private $horisontalPositions = ['left', 'center', 'right'];

    /**
     * @var array
     */
    private $verticalPositions   = ['top', 'middle', 'bottom'];

    /**
     * combine all variation of label position.
     *
     * @param bool $asText
     *
     * @return array
     */
    public function getAvailablePositions($asText = true)
    {
        $a = [];
        foreach ($this->verticalPositions as $first) {
            foreach ($this->horisontalPositions as $second) {
                $a[] = $asText ?
                    __(ucwords($first . ' ' . $second))
                    :
                    $first . '-' . $second;
            }
        }

        return $a;
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return array_merge(
            ($this->getProduct() instanceof IdentityInterface) ? $this->getProduct()->getIdentities() : [],
            [self::CACHE_TAG . '_' . $this->getId()]
        );
    }

    /**
     * Get position value of label
     * @return string
     */
    public function getCssClass()
    {
        $all = $this->getAvailablePositions(false);
        $pos = $this->getValue('pos') ? $this->getValue('pos'): 0;

        return $all[$pos];
    }

    /**
     * @return array|mixed|string
     */
    public function getStoreIds()
    {
        $storeIds = $this->getStores();
        $storeIds = explode(',', $storeIds);

        return $storeIds;
    }

    /**
     * Get label text with replacing data
     * @return string
     */
    public function getText()
    {
        $txt = $this->getValue('txt');

        preg_match_all('/{([a-zA-Z:\_0-9]+)}/', $txt, $vars);
        if (!$vars[1]) {
            return $txt;
        }
        $vars = $vars[1];
        $product = $this->getProduct();

        foreach ($vars as $var) {
            $value = $this->getCustomVariable($var, $product);
            $txt = str_replace('{' . $var . '}', $value, $txt);
        }

        return $txt;
    }

    /**
     * @param $var
     * @param $product
     * @return string
     */
    private function getCustomVariable($var, $product)
    {
        switch ($var) {
            case 'PRICE':
                $price = $this->loadPrices();
                $value = $this->convertPrice($price['price']);
                break;
            case 'SPECIAL_PRICE':
                $price = $this->loadPrices();
                $value = $this->convertPrice($price['special_price']);
                break;
            case 'FINAL_PRICE':
                $value = $this->convertPrice(
                    $this->catalogData->getTaxPrice($product, $product->getFinalPrice(), false)
                );
                break;
            case 'FINAL_PRICE_INCL_TAX':
                $value = $this->convertPrice(
                    $this->catalogData->getTaxPrice($product, $product->getFinalPrice(), true)
                );
                break;
            case 'STARTINGFROM_PRICE':
                $value = $this->convertPrice($this->getMinimalPrice($product));
                break;
            case 'STARTINGTO_PRICE':
                $value = $this->convertPrice($this->getMaximalPrice($product));
                break;
            case 'SAVE_AMOUNT':
                $price = $this->loadPrices();
                $value = $this->convertPrice($price['price'] - $price['special_price']);
                break;
            case 'SAVE_PERCENT':
                $value = $this->getPercentPrice();
                break;
            case 'BR':
                $value = '<br/>';
                break;
            case 'SKU':
                $value = $product->getSku();
                break;
            case 'NEW_FOR':
                $createdAt = strtotime($product->getCreatedAt());
                $value     = max(1, floor((time() - $createdAt) / 86400));
                break;
            case 'STOCK':
                $value = $this->getProductQty($product);
                break;
            case 'SPDL':
                $value = 0;
                $toDate = $product->getSpecialToDate();
                if ($toDate) {
                    $currentTime = $this->date->date();

                    $diff = strtotime($toDate) - strtotime($currentTime);
                    if ($diff >= 0) {
                        $value = floor($diff / (60*60*24));//days
                    }
                }
                break;
            case 'SPHL':
                $value = 0;
                $toDate = $product->getSpecialToDate();
                if ($toDate) {
                    $currentTime = $this->date->date();

                    $diff = strtotime($toDate) - strtotime($currentTime);
                    if ($diff >= 0) {
                        $value = floor($diff / (60*60));//hours
                    }
                }
                break;
            default:
                $value = $this->getDefaultValue($product, $var);
        }

        return $value;
    }

    /**
     * @return float|int
     */
    protected function getPercentPrice()
    {
        $value = 0;
        $price = $this->loadPrices();
        if ($price['price'] != 0) {
            $value = $price['price'] - $price['special_price'];
            switch ($this->helper->getModuleConfig('on_sale/rounding')) {
                case 'floor':
                    $value = floor($value * 100 / $price['price']);
                    break;
                case 'ceil':
                    $value = ceil($value * 100 / $price['price']);
                    break;
                case 'round':
                default:
                    $value = round($value * 100 / $price['price']);
                    break;
            }
        }

        return $value;
    }

    /**
     * Strip tag from price and convert it to store format
     *
     * @param $price
     *
     * @return string
     */
    private function convertPrice($price)
    {
        $store = $this->storeManager->getStore();
        return strip_tags($this->priceCurrency->convertAndFormat($price, $store));
    }

    /**
     * @param $product
     * @param $var
     *
     * @return string
     */
    private function getDefaultValue($product, $var)
    {
        $str = 'ATTR:';
        $value = '';
        if (substr($var, 0, strlen($str)) == $str) {
            $code  = trim(substr($var, strlen($str)));

            $decimal = null;
            if (false !== strpos($code, ':')) {
                $temp = explode(':', $code);
                $code = $temp[0];
                $decimal = $temp[1];
            }

            $attribute = $product->getResource()->getAttribute($code);
            if ($attribute && in_array($attribute->getFrontendInput(), ['select', 'multiselect'])) {
                $value = $product->getAttributeText($code);
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
            } else {
                $value = $product->getData($code);
            }

            if ($decimal !== null && false !== strpos($value, '.')) {
                $temp = explode('.', $value);
                $value = $temp[0] . '.' . substr($temp[1], 0, $decimal);
            }

            if (preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/", $value)) {
                $value = $this->timezone->formatDateTime(
                    new \DateTime($value),
                    \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::NONE
                );
            }
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        $style = $this->getValue('style');
        $size = $this->getImageInfo();
        if ($size && array_key_exists('w', $size)) {
            $style = 'max-width: ' . $size['w'] . '; ' . $style;
        }

        return $style;
    }

    /**
     * @return array
     */
    private function getImageInfo()
    {
        $path = $this->getValue('img');
        $path = $this->helper->getImagePath($path);
        if ($path) {
            try {
                if (strpos($path, 'svg') !== false) {
                    $xml = simplexml_load_file($path);
                    $attr = $xml->attributes();
                    $info = [(int)$attr->width . 'pt', (int)$attr->height . 'pt'];
                } else {
                    $info = getimagesize($path);
                    $info[0] .= 'px';
                    $info[1] .= 'px';
                }
            } catch (\Exception $ex) {
                return [];
            }
        } else {
            return [];
        }

        return ['w'=>$info[0], 'h'=>$info[1]];
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelId()
    {
        return $this->_getData(LabelInterface::LABEL_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelId($labelId)
    {
        $this->setData(LabelInterface::LABEL_ID, $labelId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPos()
    {
        return $this->_getData(LabelInterface::POS);
    }

    /**
     * {@inheritdoc}
     */
    public function setPos($pos)
    {
        $this->setData(LabelInterface::POS, $pos);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSingle()
    {
        return $this->_getData(LabelInterface::IS_SINGLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSingle($isSingle)
    {
        $this->setData(LabelInterface::IS_SINGLE, $isSingle);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_getData(LabelInterface::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->setData(LabelInterface::NAME, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        return $this->_getData(LabelInterface::STORES);
    }

    /**
     * {@inheritdoc}
     */
    public function setStores($stores)
    {
        $this->setData(LabelInterface::STORES, $stores);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProdTxt()
    {
        return $this->_getData(LabelInterface::PROD_TXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setProdTxt($prodTxt)
    {
        $this->setData(LabelInterface::PROD_TXT, $prodTxt);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProdImg()
    {
        return $this->_getData(LabelInterface::PROD_IMG);
    }

    /**
     * {@inheritdoc}
     */
    public function setProdImg($prodImg)
    {
        $this->setData(LabelInterface::PROD_IMG, $prodImg);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProdImageSize()
    {
        return $this->_getData(LabelInterface::PROD_IMAGE_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setProdImageSize($prodImageSize)
    {
        $this->setData(LabelInterface::PROD_IMAGE_SIZE, $prodImageSize);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProdPos()
    {
        return $this->_getData(LabelInterface::PROD_POS);
    }

    /**
     * {@inheritdoc}
     */
    public function setProdPos($prodPos)
    {
        $this->setData(LabelInterface::PROD_POS, $prodPos);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProdStyle()
    {
        return $this->_getData(LabelInterface::PROD_STYLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setProdStyle($prodStyle)
    {
        $this->setData(LabelInterface::PROD_STYLE, $prodStyle);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProdTextStyle()
    {
        return $this->_getData(LabelInterface::PROD_TEXT_STYLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setProdTextStyle($prodTextStyle)
    {
        $this->setData(LabelInterface::PROD_TEXT_STYLE, $prodTextStyle);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCatTxt()
    {
        return $this->_getData(LabelInterface::CAT_TXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCatTxt($catTxt)
    {
        $this->setData(LabelInterface::CAT_TXT, $catTxt);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCatImg()
    {
        return $this->_getData(LabelInterface::CAT_IMG);
    }

    /**
     * {@inheritdoc}
     */
    public function setCatImg($catImg)
    {
        $this->setData(LabelInterface::CAT_IMG, $catImg);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCatPos()
    {
        return $this->_getData(LabelInterface::CAT_POS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCatPos($catPos)
    {
        $this->setData(LabelInterface::CAT_POS, $catPos);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCatStyle()
    {
        return $this->_getData(LabelInterface::CAT_STYLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCatStyle($catStyle)
    {
        $this->setData(LabelInterface::CAT_STYLE, $catStyle);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCatImageSize()
    {
        return $this->_getData(LabelInterface::CAT_IMAGE_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCatImageSize($catImageSize)
    {
        $this->setData(LabelInterface::CAT_IMAGE_SIZE, $catImageSize);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCatTextStyle()
    {
        return $this->_getData(LabelInterface::CAT_TEXT_STYLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCatTextStyle($catTextStyle)
    {
        $this->setData(LabelInterface::CAT_TEXT_STYLE, $catTextStyle);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsNew()
    {
        return $this->_getData(LabelInterface::IS_NEW);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsNew($isNew)
    {
        $this->setData(LabelInterface::IS_NEW, $isNew);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsSale()
    {
        return $this->_getData(LabelInterface::IS_SALE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsSale($isSale)
    {
        $this->setData(LabelInterface::IS_SALE, $isSale);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecialPriceOnly()
    {
        return $this->_getData(LabelInterface::SPECIAL_PRICE_ONLY);
    }

    /**
     * {@inheritdoc}
     */
    public function setSpecialPriceOnly($specialPriceOnly)
    {
        $this->setData(LabelInterface::SPECIAL_PRICE_ONLY, $specialPriceOnly);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockLess()
    {
        return $this->_getData(LabelInterface::STOCK_LESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStockLess($stockLess)
    {
        $this->setData(LabelInterface::STOCK_LESS, $stockLess);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockMore()
    {
        return $this->_getData(LabelInterface::STOCK_MORE);
    }

    /**
     * {@inheritdoc}
     */
    public function setStockMore($stockMore)
    {
        $this->setData(LabelInterface::STOCK_MORE, $stockMore);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockStatus()
    {
        return $this->_getData(LabelInterface::STOCK_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStockStatus($stockStatus)
    {
        $this->setData(LabelInterface::STOCK_STATUS, $stockStatus);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromDate()
    {
        return $this->_getData(LabelInterface::FROM_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFromDate($fromDate)
    {
        $this->setData(LabelInterface::FROM_DATE, $fromDate);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getToDate()
    {
        return $this->_getData(LabelInterface::TO_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setToDate($toDate)
    {
        $this->setData(LabelInterface::TO_DATE, $toDate);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDateRangeEnabled()
    {
        return $this->_getData(LabelInterface::DATE_RANGE_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setDateRangeEnabled($dateRangeEnabled)
    {
        $this->setData(LabelInterface::DATE_RANGE_ENABLED, $dateRangeEnabled);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromPrice()
    {
        return $this->_getData(LabelInterface::FROM_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFromPrice($fromPrice)
    {
        $this->setData(LabelInterface::FROM_PRICE, $fromPrice);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getToPrice()
    {
        return $this->_getData(LabelInterface::TO_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setToPrice($toPrice)
    {
        $this->setData(LabelInterface::TO_PRICE, $toPrice);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getByPrice()
    {
        return $this->_getData(LabelInterface::BY_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setByPrice($byPrice)
    {
        $this->setData(LabelInterface::BY_PRICE, $byPrice);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceRangeEnabled()
    {
        return $this->_getData(LabelInterface::PRICE_RANGE_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceRangeEnabled($priceRangeEnabled)
    {
        $this->setData(LabelInterface::PRICE_RANGE_ENABLED, $priceRangeEnabled);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroupIds()
    {
        return $this->_getData(LabelInterface::CUSTOMER_GROUP_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroupIds($customerGroupIds)
    {
        $this->setData(LabelInterface::CUSTOMER_GROUP_IDS, $customerGroupIds);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCondSerialize()
    {
        return $this->_getData(LabelInterface::COND_SERIALIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCondSerialize($condSerialize)
    {
        $this->setData(LabelInterface::COND_SERIALIZE, $condSerialize);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroupEnabled()
    {
        return $this->_getData(LabelInterface::CUSTOMER_GROUP_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroupEnabled($customerGroupEnabled)
    {
        $this->setData(LabelInterface::CUSTOMER_GROUP_ENABLED, $customerGroupEnabled);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUseForParent()
    {
        return $this->_getData(LabelInterface::USE_FOR_PARENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUseForParent($useForParent)
    {
        $this->setData(LabelInterface::USE_FOR_PARENT, $useForParent);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_getData(LabelInterface::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->setData(LabelInterface::STATUS, $status);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductStockEnabled()
    {
        return $this->_getData(LabelInterface::PRODUCT_STOCK_ENABLED);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductStockEnabled($productStockEnabled)
    {
        $this->setData(LabelInterface::PRODUCT_STOCK_ENABLED, $productStockEnabled);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStockHigher()
    {
        return $this->_getData(LabelInterface::STOCK_HIGHER);
    }

    /**
     * {@inheritdoc}
     */
    public function setStockHigher($stockHigher)
    {
        $this->setData(LabelInterface::STOCK_HIGHER, $stockHigher);

        return $this;
    }
}
