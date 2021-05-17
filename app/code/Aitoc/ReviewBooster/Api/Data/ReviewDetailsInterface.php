<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Api\Data;

/**
 * Interface ReviewDetailsInterface
 */
interface ReviewDetailsInterface
{
    /**
     * Constants defined for keys of data array
     */
    const EXTENDED_ID = 'extended_id';
    const REVIEW_ID = 'review_id';
    const PRODUCT_ADVANTAGES = 'product_advantages';
    const PRODUCT_DISADVANTAGES = 'product_disadvantages';
    const REVIEW_HELPFUL = 'review_helpful';
    const REVIEW_UNHELPFUL = 'review_unhelpful';
    const REVIEW_REPORTED = 'review_reported';
    const CUSTOMER_VERIFIED = 'customer_verified';
    const COMMENT = 'comment';
    const ADMIN_TITLE = 'admin_title';

    /**
     * @return int
     */
    public function getExtendedId();

    /**
     * @param int $extendedId
     *
     * @return $this
     */
    public function setExtendedId($extendedId);

    /**
     * @return int
     */
    public function getReviewId();

    /**
     * @param int $reviewId
     *
     * @return $this
     */
    public function setReviewId($reviewId);

    /**
     * @return string
     */
    public function getProductAdvantages();

    /**
     * @param string $productAdvantages
     *
     * @return $this
     */
    public function setProductAdvantages($productAdvantages);

    /**
     * @return string
     */
    public function getProductDisadvantages();

    /**
     * @param string $productDisadvantages
     *
     * @return $this
     */
    public function setProductDisadvantages($productDisadvantages);

    /**
     * @return int
     */
    public function getReviewHelpful();

    /**
     * @param int $reviewHelpful
     *
     * @return $this
     */
    public function setReviewHelpful($reviewHelpful);

    /**
     * @return int
     */
    public function getReviewUnhelpful();

    public function incrementHelpful();

    /**
     * @param int $reviewUnhelpful
     *
     * @return $this
     */
    public function setReviewUnhelpful($reviewUnhelpful);

    public function incrementUnhelpful();

    /**
     * @return int
     */
    public function getReviewReported();

    /**
     * @param int $reviewReported
     *
     * @return $this
     */
    public function setReviewReported($reviewReported);

    public function incrementReviewReported();

    /**
     * @return int
     */
    public function getCustomerVerified();

    /**
     * @param int $customerVerified
     *
     * @return $this
     */
    public function setCustomerVerified($customerVerified);

    /**
     * @return string
     */
    public function getComment();

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment);

    /**
     * @return string
     */
    public function getAdminTitle();

    /**
     * @param string $adminTitle
     *
     * @return $this
     */
    public function setAdminTitle($adminTitle);

    /**
     * Add data to the object.
     *
     * Retains previous data in the object.
     *
     * @param array $arr
     * @return $this
     */
    public function addData(array $arr);

    /**
     * @return array
     */
    public function getData();
}
