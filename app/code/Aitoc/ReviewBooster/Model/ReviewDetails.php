<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Model;

use Aitoc\ReviewBooster\Api\Data\ReviewDetailsInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Review\Model\Review;
use Magento\Review\Model\ReviewFactory;
use Aitoc\ReviewBooster\Model\ResourceModel\ReviewDetails as ReviewResourceModel;
use Magento\Store\Model\StoreManager;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class ReviewDetails
 */
class ReviewDetails extends AbstractModel implements ReviewDetailsInterface
{
    /**
     * Ten years
     */
    const COOKIE_DURATION = 315360000;

    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var CookieManagerInterface
     */
    protected $cookie;

    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadata;

    /**
     * @param ReviewFactory $reviewFactory
     * @param StoreManager $storeManager
     * @param CookieManagerInterface $cookie
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        ReviewFactory $reviewFactory,
        StoreManager $storeManager,
        CookieManagerInterface $cookie,
        CookieMetadataFactory $cookieMetadataFactory,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->storeManager = $storeManager;
        $this->cookie = $cookie;
        $this->cookieMetadata = $cookieMetadataFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }


    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ReviewResourceModel::class);
    }

    /**
     * Load review
     *
     * @param int $reviewId
     * @return Review|bool
     * @throws NoSuchEntityException
     */
    public function loadReview($reviewId)
    {
        if (!$reviewId) {
            return false;
        }
        $review = $this->reviewFactory->create()->load($reviewId);
        if (!$review->getId()
            || !$review->isApproved()
            || !$review->isAvailableOnStore($this->storeManager->getStore())
        ) {
            return false;
        }

        return $review;
    }



    /**
     * Check review status
     *
     * @param int $reviewId
     * @return bool
     */
    public function checkReviewStatus($reviewId)
    {
        $cookieName = $this->getCookieName();//todo: debug it
        $typeCookie = $this->cookie->getCookie($cookieName);

        if (isset($typeCookie[$reviewId])) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get cookie name
     *
     * @param int $reviewId
     * @param string $cookieName
     * @return string
     */
    public function getCookieNameForSave($reviewId, $cookieName)
    {
        $cookieName = $cookieName . '[' . $reviewId . ']';

        return $cookieName;
    }

    /**
     * Get cookie duration
     *
     * @return int
     */
    public function getCookieDuration()
    {
        return self::COOKIE_DURATION;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getExtendedId();
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setExtendedId($id);
    }

    /**
     * @return int
     */
    public function getExtendedId()
    {
        return $this->getData(self::EXTENDED_ID);
    }

    /**
     * @param int $extendedId
     * @return $this
     */
    public function setExtendedId($extendedId)
    {
        return $this->setData(self::EXTENDED_ID, $extendedId);
    }

    /**
     * @return int
     */
    public function getReviewId()
    {
        return $this->getData(self::REVIEW_ID);
    }

    /**
     * @param int $reviewId
     * @return $this
     */
    public function setReviewId($reviewId)
    {
        return $this->setData(self::REVIEW_ID, $reviewId);
    }

    /**
     * @return string
     */
    public function getProductAdvantages()
    {
        return $this->getData(self::PRODUCT_ADVANTAGES);
    }

    /**
     * @param string $productAdvantages
     * @return $this
     */
    public function setProductAdvantages($productAdvantages)
    {
        return $this->setData(self::PRODUCT_ADVANTAGES, $productAdvantages);
    }

    /**
     * @return string
     */
    public function getProductDisadvantages()
    {
        return $this->getData(self::PRODUCT_DISADVANTAGES);
    }

    /**
     * @param string $productDisadvantages
     * @return $this
     */
    public function setProductDisadvantages($productDisadvantages)
    {
        return $this->setData(self::PRODUCT_DISADVANTAGES, $productDisadvantages);
    }

    /**
     * @return int
     */
    public function getReviewHelpful()
    {
        return $this->getData(self::REVIEW_HELPFUL);
    }

    /**
     * @param int $reviewHelpful
     * @return $this
     */
    public function setReviewHelpful($reviewHelpful)
    {
        return $this->setData(self::REVIEW_HELPFUL, $reviewHelpful);
    }

    public function incrementHelpful()
    {
        $helpfulCount = $this->getReviewHelpful();
        $this->setReviewHelpful(++$helpfulCount);
    }

    /**
     * @return int
     */
    public function getReviewUnhelpful()
    {
        return $this->getData(self::REVIEW_UNHELPFUL);
    }

    /**
     * @param int $reviewUnhelpful
     * @return $this
     */
    public function setReviewUnhelpful($reviewUnhelpful)
    {
        return $this->setData(self::REVIEW_UNHELPFUL, $reviewUnhelpful);
    }

    public function incrementUnhelpful()
    {
        $unhelpfulCount = $this->getReviewHelpful();
        $this->setReviewUnhelpful(++$unhelpfulCount);
    }

    /**
     * @return int
     */
    public function getReviewReported()
    {
        return $this->getData(self::REVIEW_REPORTED);
    }

    /**
     * @param int $reviewReported
     * @return $this
     */
    public function setReviewReported($reviewReported)
    {
        return $this->setData(self::REVIEW_REPORTED, $reviewReported);
    }

    public function incrementReviewReported()
    {
        $reviewReported = $this->getReviewReported();
        $reviewReported++;

        $this->setReviewReported($reviewReported);
    }

    /**
     * @return int
     */
    public function getCustomerVerified()
    {
        return $this->getData(self::CUSTOMER_VERIFIED);
    }

    /**
     * @param int $customerVerified
     * @return $this
     */
    public function setCustomerVerified($customerVerified)
    {
        return $this->setData(self::CUSTOMER_VERIFIED, $customerVerified);
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * @return string
     */
    public function getAdminTitle()
    {
        return $this->getData(self::ADMIN_TITLE);
    }

    /**
     * @param string $adminTitle
     * @return $this
     */
    public function setAdminTitle($adminTitle)
    {
        return $this->setData(self::ADMIN_TITLE, $adminTitle);
    }
}
