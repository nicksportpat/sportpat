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

use Aitoc\FollowUpEmails\Helper\Website as WebsiteHelper;
use Aitoc\ReviewBooster\Api\Data\MagentoReviewInterface;
use Aitoc\ReviewBooster\Api\Data\ReviewDetailsInterface;
use Aitoc\ReviewBooster\Api\Service\ConfigProviderInterface as ConfigHelperInterface;
use Aitoc\ReviewBooster\Helper\AdminReviewFormModifier;
use Magento\Catalog\Api\Data\ProductInterface;
use Aitoc\ReviewBooster\Api\Data\Source\Table\ReviewDetailInterface as ReviewDetailTableInterface;
use Aitoc\ReviewBooster\Api\Data\Source\Table\ReviewInterface as ReviewTableInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Review\Model\Review;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Notification
 */
class Notification
{
    const TEMPLATE_IDENTIFIER = 'review_booster_admin_notification_template';
    const COMMENT_NOTIFICATION_TEMPLATE = 'review_booster_comment_notification_template';

    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var ConfigHelperInterface
     */
    private $configHelper;

    /**
     * @var WebsiteHelper
     */
    private $websiteHelper;

    /**
     * @param SenderResolverInterface $senderResolver
     * @param ProductRepository $productRepository
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepository $customerRepository
     * @param ConfigHelperInterface $configHelper
     * @param WebsiteHelper $websiteHelper
     */
    public function __construct(
        SenderResolverInterface $senderResolver,
        ProductRepository $productRepository,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        CustomerRepository $customerRepository,
        ConfigHelperInterface $configHelper,
        WebsiteHelper $websiteHelper
    ) {
        $this->senderResolver = $senderResolver;
        $this->productRepository = $productRepository;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->configHelper = $configHelper;
        $this->websiteHelper = $websiteHelper;
    }

    /**
     * @param Review $review
     * @param ReviewDetailsInterface $extendedReview
     * @param bool $isReviewExist
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function processNotificationSending(Review $review, ReviewDetailsInterface $extendedReview, $isReviewExist = false)
    {
        $websiteId = $this->getWebsiteIdByReview($review);
        $this->sendReviewNotificationToAdminIfRequired($review, $isReviewExist, $websiteId);
        $this->sendCommentNotificationToCustomerIfRequired($review, $extendedReview);
    }

    /**
     * @param Review $review
     * @return int
     * @throws NoSuchEntityException
     */
    private function getWebsiteIdByReview(Review $review)
    {
        $storeId = $this->getStoreIdByReview($review);

        return $this->getWebsiteIdByStoreId($storeId);
    }

    /**
     * @param Review $review
     * @return int
     */
    private function getStoreIdByReview(Review $review)
    {
        return $review->getData(MagentoReviewInterface::COLUMN_NAME_STORE_ID);
    }

    /**
     * @param int $storeId
     * @return int
     * @throws NoSuchEntityException
     */
    private function getWebsiteIdByStoreId($storeId)
    {
        return $this->websiteHelper->getWebsiteIdByStoreId($storeId);
    }

    /**
     * @param Review $review
     * @param $isReviewExist
     * @param int $websiteId
     * @throws MailException
     * @throws NoSuchEntityException
     */
    private function sendReviewNotificationToAdminIfRequired(Review $review, $isReviewExist, $websiteId)
    {
        if ($isReviewExist && $this->getConfigIsEmailNotificationEnabled($websiteId)) {
            $this->sendReviewNotificationToAdmin($review);
        }
    }

    /**
     * @param int $websiteId
     * @return bool
     */
    private function getConfigIsEmailNotificationEnabled($websiteId)
    {
        return $this->configHelper->isReviewEmailNotificationEnabled($websiteId);
    }

    /**
     * Send review notification to admin
     *
     * @param Review|AbstractModel $review
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendReviewNotificationToAdmin($review)
    {
        $productName = $this->getProductNameByReview($review);

        $storeId = $this->getStoreIdByReview($review);
        $websiteId = $this->getWebsiteIdByStoreId($storeId);
        $emailRecipient = $this->getConfigEmailNotificationRecipient($websiteId);
        $recipientData = $this->resolveRecipientData($emailRecipient);

        $transport = $this->createTransport(self::TEMPLATE_IDENTIFIER, $storeId, $productName, $recipientData['email'], $recipientData['name']);
        $transport->sendMessage();
    }

    /**
     * @param ReviewDetails|AbstractModel $review
     * @return null|string
     * @throws NoSuchEntityException
     */
    private function getProductNameByReview($review)
    {
        $productId = $review->getData(ReviewTableInterface::COLUMN_NAME_ENTITY_PK_VALUE);
        $product = $this->getProductById($productId);
        $productName = $product->getName();

        return $productName;
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
     * @param int $websiteId
     * @return bool
     */
    private function getConfigEmailNotificationRecipient($websiteId)
    {
        return $this->configHelper->getReviewEmailNotificationRecipient($websiteId);
    }

    /**
     * @param string $emailRecipient
     * @return array
     * @throws MailException
     */
    private function resolveRecipientData($emailRecipient)
    {
        return $this->senderResolver->resolve($emailRecipient);
    }

    /**
     * @param string $templateIdentifier
     * @param int $storeId
     * @param string $productName
     * @param string $recipientEmail
     * @param string $recipientName
     * @return TransportInterface
     */
    private function createTransport($templateIdentifier, $storeId, $productName, $recipientEmail, $recipientName = '')
    {
        return $this->transportBuilder
            ->setTemplateIdentifier(
                $templateIdentifier
            )
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $storeId,
                ]
            )
            ->setTemplateVars(
                [
                    'product_name' => $productName
                ]
            )
            ->setFromByScope('general')
            ->addTo(
                $recipientEmail,
                $recipientName
            )
            ->getTransport();
    }

    /**
     * @param Review $review
     * @param ReviewDetailsInterface $extendedReview
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function sendCommentNotificationToCustomerIfRequired(Review $review, ReviewDetailsInterface $extendedReview)
    {
        if ($extendedReview->getComment()
            && $review->getData(MagentoReviewInterface::COLUMN_NAME_CUSTOMER_ID)
            && $review->getData(AdminReviewFormModifier::FIELD_ID_SEND_TO_CUSTOMER)
        ) {
            $this->sendCommentNotificationToCustomer($review);
        }
    }

    /**
     * @param Review $review
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function sendCommentNotificationToCustomer(Review $review)
    {
        $customerEmailAddress = $this->getCustomerEmailAddressByReview($review);
        $productName = $this->getProductNameByReview($review);
        $storeId = $this->getStoreIdByReview($review);

        $transport = $this->createTransport(
            self::COMMENT_NOTIFICATION_TEMPLATE,
            $storeId,
            $productName,
            $customerEmailAddress
        );

        $transport->sendMessage();
    }

    /**
     * @param Review $review
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getCustomerEmailAddressByReview(Review $review)
    {
        $customerId = $this->getCustomerIdByReview($review);
        $customer = $this->getCustomerById($customerId);

        return $customer->getEmail();
    }

    /**
     * @param Review $review
     * @return int|null
     */
    private function getCustomerIdByReview(Review $review)
    {
        return $review->getData(MagentoReviewInterface::COLUMN_NAME_CUSTOMER_ID);
    }

    /**
     * @param int $customerId
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getCustomerById($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }
}
