<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Block;

use Aitoc\FollowUpEmails\Api\Contoller\Account\Login\RequestParamNameInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aitoc\FollowUpEmails\Helper\Url;

/**
 * Class Products
 */
class Products extends Template
{
    const ROUTE_PATH_AUTOLOGIN_TRACKED_URL = 'followup/transitto/product';

    const DATA_KEY_EMAIL_ID = 'email_id';
    const DATA_KEY_STORE_ID = 'store_id';
    const DATA_KEY_SECRET_CODE = 'secret_code';

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Url
     */
    private $url;

    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        ProductRepositoryInterface $productRepository,
        Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->urlBuilder = $urlBuilder;
        $this->productRepository = $productRepository;
        $this->url = $url;
    }

    /**
     * @param ProductInterface $product
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    public function getFullProductByProduct(ProductInterface $product)
    {
        $productId = $product->getId();

        return $this->getProductById($productId);
    }

    /**
     * @param int $productId
     * @return ProductInterface
     * @throws NoSuchEntityException
     */
    private function getProductById($productId)
    {
        return $this->productRepository->getById($productId);
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    public function getProductTrackedLoginUrl(ProductInterface $product)
    {
        $routeParams = $this->getRouteParamsByProduct($product);

        return $this->getUrl(self::ROUTE_PATH_AUTOLOGIN_TRACKED_URL, $routeParams);
    }

    /**
     * @param ProductInterface $product
     * @return array
     */
    protected function getRouteParamsByProduct(ProductInterface $product)
    {
        $productId = $product->getId();
        $emailId = $this->getData(self::DATA_KEY_EMAIL_ID);
        $secretCode = $this->getData(self::DATA_KEY_SECRET_CODE);

        return [
            RequestParamNameInterface::EMAIL_ID => $emailId,
            RequestParamNameInterface::SECRET_CODE => $secretCode,
            RequestParamNameInterface::PRODUCT_ID => $productId,
            RequestParamNameInterface::ANCHOR => null,
        ];
    }

    /**
     * @param string|null $routePath
     * @param array|null $routeParams
     * @return string
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        return $this->url->prepareUtmByEmailId(
                $this->urlBuilder->getUrl($routePath, $routeParams),
                $this->getData(self::DATA_KEY_EMAIL_ID)
            );
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    public function getProductImageUrl(ProductInterface $product)
    {
        if(!$productImages = $this->getProductImages($product)) {
            return null;
        }

        $productImage = reset($productImages);

        return $this->url->prepareUtmByEmailId($productImage->getUrl(), $this->getData(self::DATA_KEY_EMAIL_ID));
    }

    /**
     * @param ProductInterface|Product $product
     * @return DataObject[]
     */
    private function getProductImages(ProductInterface $product)
    {
        if (!$mediaGalleryImageCollection = $product->getMediaGalleryImages()) {
            return [];
        }

        return $mediaGalleryImageCollection->getItems();
    }
}
