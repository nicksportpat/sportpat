<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Shopbybrand\Block\Link;

use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\Shopbybrand\Block\Brand;

/**
 * Class CategoryMenu
 *
 * @package Mageplaza\Shopbybrand\Block\Link
 */
class CategoryMenu extends Brand
{
    /**
     * @return int
     */
    public function getBrandCount()
    {
        $brands = $this->getCollection();
        $count  = ceil(count($brands) / 12);

        return (int) $count;
    }

    /**
     * @return array
     */
    public function getBrands()
    {
        $brands = $this->getCollection();
        $result = [];
        $i      = 0;
        $count  = 0;
        foreach ($brands as $brand) {
            $count++;
            $result[$i][] = $brand;
            if ($count === 12) {
                $count = 0;
                $i++;
            }
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function enableDropdownMenu()
    {
        return $this->helper->getModuleConfig('general/show_dropdown');
    }

    /**
     * @return string
     */
    public function getBrandTitle()
    {
        return $this->helper->getBrandTitle();
    }

    /**
     * @param \Mageplaza\Shopbybrand\Model\Brand $brand
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBrandUrl($brand = null)
    {
        return $this->helper->getBrandUrl($brand);
    }

    /**
     * @param \Mageplaza\Shopbybrand\Model\Brand $brand
     *
     * @return string
     */
    public function getBrandImageUrl($brand)
    {
        return $this->helper->getBrandImageUrl($brand);
    }
}
