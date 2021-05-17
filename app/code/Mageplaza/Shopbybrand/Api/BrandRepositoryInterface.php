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

namespace Mageplaza\Shopbybrand\Api;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Mageplaza\Shopbybrand\Api\Data\BrandCategoryInterface;
use Mageplaza\Shopbybrand\Api\Data\BrandInterface;

/**
 * Class BrandRepositoryInterface
 * @package Mageplaza\Shopbybrand\Api
 */
interface BrandRepositoryInterface
{
    /**
     * Get brand list
     *
     * @return BrandInterface[]
     */
    public function getBrandList();

    /**
     * Get brand feature list
     *
     * @return BrandInterface[]
     */
    public function getFeatureBrand();

    /**
     * @param string $name
     *
     * @return BrandInterface[]
     */
    public function getBrandByName($name);

    /**
     * @param string $optionId
     *
     * @return ProductInterface[]
     */
    public function getProductList($optionId);

    /**
     * @param string $sku
     * @param int|null $storeId
     *
     * @return BrandInterface
     * @throws NoSuchEntityException
     */
    public function getBrandBySku($sku, $storeId = null);

    /**
     * @param string $optionId
     * @param string $sku
     * @param int|null $storeId
     *
     * @return ProductInterface
     * @throws NoSuchEntityException
     * @throws InputException
     * @throws StateException
     * @throws CouldNotSaveException
     */
    public function setProduct($optionId, $sku, $storeId = null);

    /**
     * @param string $sku
     *
     * @return bool Will returned True if deleted
     * @throws NoSuchEntityException
     * @throws InputException
     * @throws StateException
     * @throws CouldNotSaveException
     */
    public function deleteProduct($sku);

    /**
     * Add option to brand
     *
     * @param BrandInterface $option
     *
     * @return bool
     * @throws StateException
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function add($option);

    /**
     * @param string $optionId
     * @param BrandInterface $option
     *
     * @return bool
     * @throws StateException
     * @throws NoSuchEntityException
     * @throws InputException
     */
    public function update($optionId, $option);

    /**
     * Delete option from brand
     *
     * @param string $optionId
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws InputException
     * @throws StateException
     */
    public function delete($optionId);

    /**
     * @return BrandCategoryInterface[]
     */
    public function getCategory();

    /**
     * @param string $categoryId
     *
     * @return BrandCategoryInterface
     * @throws NoSuchEntityException
     */
    public function getCategoryById($categoryId);

    /**
     * @param BrandCategoryInterface $category
     *
     * @return BrandCategoryInterface
     * @throws StateException
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function addCategory($category);

    /**
     * @param string $categoryId
     * @param BrandCategoryInterface $category
     *
     * @return BrandCategoryInterface
     * @throws StateException
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function updateCategory($categoryId, $category);

    /**
     * @param string $categoryId
     *
     * @return bool
     * @throws StateException
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function deleteCategory($categoryId);
}
