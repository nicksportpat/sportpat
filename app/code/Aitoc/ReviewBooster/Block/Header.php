<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Block;

use Aitoc\ReviewBooster\Api\Service\ConfigProviderInterface as ConfigHelperInterface;
use Aitoc\ReviewBooster\Helper\Rating as RatingHelper;
use Aitoc\ReviewBooster\Helper\RegistryDataProvider;
use Aitoc\ReviewBooster\Model\MagentoReviewManagement;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Directory\Model\Currency;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Header
 */
class Header extends Template
{
    /**
     * const
     */
    const STARS_PERCENT = '20';

    /**
     * @var RegistryDataProvider
     */
    private $registryDataProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var MagentoReviewManagement
     */
    private $magentoReviewManagement;

    /**
     * @var ConfigHelperInterface
     */
    private $configHelper;

    /**
     * @var RatingHelper
     */
    private $ratingHelper;

    /**
     * @param Context $context
     * @param ConfigHelperInterface $configHelper
     * @param RegistryDataProvider $registryDataProvider
     * @param MagentoReviewManagement $magentoReviewManagement
     * @param RatingHelper $ratingHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ConfigHelperInterface $configHelper,
        RegistryDataProvider $registryDataProvider,
        MagentoReviewManagement $magentoReviewManagement,
        RatingHelper $ratingHelper,
        array $data = []
    ) {
        $this->configHelper = $configHelper;
        $this->registryDataProvider = $registryDataProvider;
        $this->storeManager = $context->getStoreManager();
        $this->magentoReviewManagement = $magentoReviewManagement;
        $this->ratingHelper = $ratingHelper;

        parent::__construct($context, $data);
    }

    /**
     * @return int
     */
    public function getCurrentApprovedReviewsCount()
    {
        $currentProductId = $this->getCurrentProductId();

        return $this->getApprovedReviewsCountByProductId($currentProductId);
    }

    /**
     * @return int
     */
    private function getCurrentProductId()
    {
        return $this->getCurrentProduct()->getId();
    }

    /**
     * @return bool|ProductInterface|Product
     */
    public function getCurrentProduct()
    {
        $registryCurrentProduct = $this->getRegistryCurrentProduct();

        return $registryCurrentProduct ? $registryCurrentProduct : false;
    }

    /**
     * @return ProductInterface
     */
    private function getRegistryCurrentProduct()
    {
        return $this->registryDataProvider->getCurrentProduct();
    }

    /**
     * @param int $productId
     * @return int
     */
    private function getApprovedReviewsCountByProductId($productId)
    {
        return $this->magentoReviewManagement->getApprovedCountByProductId($productId);
    }

    /**
     * @return float
     * @throws NoSuchEntityException
     */
    public function getRatingSummary()
    {
        $currentProduct = $this->getCurrentProduct();
        $rating = $this->getRatingSummaryByProduct($currentProduct);
        $ratingSummary = $rating / self::STARS_PERCENT;

        return $ratingSummary;
    }

    /**
     * @param ProductInterface $product
     * @return string
     * @throws NoSuchEntityException
     */
    private function getRatingSummaryByProduct(ProductInterface $product)
    {
        return $this->ratingHelper->getRatingSummary($product);
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCurrencyCode()
    {
        return $this->getStoreCurrentCurrencyCode();
    }

    /**
     * @throws NoSuchEntityException
     */
    private function getStoreCurrentCurrencyCode()
    {
        $this->getStoreCurrentCurrency()->getCode();
    }

    /**
     * @return Currency
     * @throws NoSuchEntityException
     */
    private function getStoreCurrentCurrency()
    {
        return $this->getStore()->getCurrentCurrency();
    }

    /**
     * @return StoreInterface|Store
     * @throws NoSuchEntityException
     */
    private function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function getConfigIsAddRichSnippets()
    {
        $currentWebsiteId = $this->getCurrentWebsiteId();

        return $this->configHelper->isAddRichSnippets($currentWebsiteId);
    }

    /**
     * @throws LocalizedException
     */
    private function getCurrentWebsiteId()
    {
        $this->storeManager->getWebsite()->getId();
    }
}
