<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Observer;

use Aitoc\ReviewBooster\Api\Data\ReviewDetailsInterface;
use Aitoc\ReviewBooster\Api\Data\Source\Table\ReviewDetailInterface as ReviewDetailTableInterface;
use Aitoc\ReviewBooster\Api\ReviewDetailsRepositoryInterface;
use Aitoc\ReviewBooster\Helper\Image as ReviewImageHelper;
use Aitoc\ReviewBooster\Model\Notification;
use Aitoc\ReviewBooster\Model\OrderManagement;
use Aitoc\ReviewBooster\Model\ReviewDetails as ExtendedReview;
use Aitoc\ReviewBooster\Model\ReviewDetailsFactory as ReviewDetailsFactory;
use Aitoc\ReviewBooster\Model\ReviewDetailsRepository as ExtendedReviewRepository;
use Closure;
use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Review\Model\Review as MagentoReview;

/**
 * Class SaveExtendedData
 */
class SaveExtendedData implements ObserverInterface
{
    const EVENT_DATA_KEY_OBJECT = 'object';

    /**
     * @var ExtendedReviewRepository
     */
    private $extendedReviewRepository;

    /**
     * @var ReviewDetailsFactory
     */
    private $reviewDetailsFactory;

    /**
     * @var Notification
     */
    private $notificationModel;

    /**
     * @var ReviewImageHelper
     */
    private $reviewImageHelper;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var OrderManagement
     */
    private $orderManagement;

    /**
     * @param ReviewDetailsRepositoryInterface $reviewRepository
     * @param ReviewDetailsFactory $reviewDetailsFactory
     * @param Notification $notificationModel
     * @param ReviewImageHelper $reviewImageHelper
     * @param ManagerInterface $messageManager
     * @param OrderManagement $orderManagement
     */
    public function __construct(
        ReviewDetailsRepositoryInterface $reviewRepository,
        ReviewDetailsFactory $reviewDetailsFactory,
        Notification $notificationModel,
        ReviewImageHelper $reviewImageHelper,
        ManagerInterface $messageManager,
        OrderManagement $orderManagement
    ) {
        $this->extendedReviewRepository = $reviewRepository;
        $this->reviewDetailsFactory = $reviewDetailsFactory;
        $this->notificationModel = $notificationModel;
        $this->reviewImageHelper = $reviewImageHelper;
        $this->messageManager = $messageManager;
        $this->orderManagement = $orderManagement;
    }

    /**
     * Save review extended data and send notification to admin
     *
     * @param AbstractDb $object
     * @param Closure $proceed
     * @param AbstractModel $review
     * @return array
     * @throws Exception
     * @throws FileSystemException
     * @throws NoSuchEntityException
     */

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        try {
            $review = $this->getMagentoReviewByObserver($observer);

            if (!$this->isMagentoReviewContainsExtendedReviewData($review)) {
                return;
            }

            $isExtendedReviewExist = $this->isExtendedReviewExists($review);
            $extendedReview = $this->saveExtendedReviewByReview($review);

            $this->processEmailNotification($review, $extendedReview, $isExtendedReviewExist);
        } catch (Exception $e) {
            $this->addErrorMessage($e);
        }
    }

    /**
     * @param Observer $observer
     * @return MagentoReview
     */
    private function getMagentoReviewByObserver(Observer $observer)
    {
        $event = $observer->getEvent();

        return $event->getData(self::EVENT_DATA_KEY_OBJECT);
    }

    /**
     * @param MagentoReview $magentoReview
     * @return bool
     */
    private function isMagentoReviewContainsExtendedReviewData(MagentoReview $magentoReview)
    {
        return (bool) $this->getExtendedReviewDataByMagentoReview($magentoReview);
    }

    /**
     * @param MagentoReview $magentoReview
     * @return array
     */
    private function getExtendedReviewDataByMagentoReview(MagentoReview $magentoReview)
    {
        $knownDataKeys = [
            ReviewDetailsInterface::PRODUCT_ADVANTAGES,
            ReviewDetailsInterface::PRODUCT_DISADVANTAGES,
            ReviewDetailsInterface::COMMENT,
            ReviewDetailsInterface::ADMIN_TITLE,
        ];

        $magentoReviewData = $magentoReview->getData();

        return array_intersect_key($magentoReviewData, array_fill_keys($knownDataKeys, null));
    }

    /**
     * @param MagentoReview $review
     * @return bool
     */
    private function isExtendedReviewExists(MagentoReview $review)
    {
        $reviewId = $review->getId();

        return (bool) $this->getExtendedReviewByReviewId($reviewId);
    }

    /**
     * @param int $reviewId
     * @return ReviewDetailsInterface
     */
    private function getExtendedReviewByReviewId($reviewId)
    {
        return $this->extendedReviewRepository->getByReviewId($reviewId);
    }

    /**
     * @param MagentoReview $magentoReview
     * @return ReviewDetailsInterface|ExtendedReview
     * @throws CouldNotSaveException
     * @throws Exception
     */
    private function saveExtendedReviewByReview(MagentoReview $magentoReview)
    {
        $reviewId = $magentoReview->getId();
        $extendedReview = $this->getOrCreateExtendedReviewByReviewId($reviewId);
        $this->updateExtendedReviewByMagentoReview($extendedReview, $magentoReview);
        $this->saveExtendedReview($extendedReview);

        $this->processImageSaving($reviewId);

        return $extendedReview;
    }

    /**
     * @param int $reviewId
     * @return ReviewDetailsInterface|ExtendedReview
     */
    private function getOrCreateExtendedReviewByReviewId($reviewId)
    {
        $extendedReview = $this->getExtendedReviewByReviewId($reviewId);

        if ($extendedReview) {
            return $extendedReview;
        }

        $extendedReview = $this->createExtendedReview();
        $extendedReview->setReviewId($reviewId);

        return $extendedReview;
    }

    /**
     * @return ExtendedReview
     */
    private function createExtendedReview()
    {
        return $this->reviewDetailsFactory->create();
    }

    /**
     * @param ReviewDetailsInterface $extendedReview
     * @param MagentoReview $magentoReview
     */
    private function updateExtendedReviewByMagentoReview(ReviewDetailsInterface $extendedReview, MagentoReview $magentoReview)
    {
        $extendedReviewData = $this->getExtendedReviewDataByMagentoReview($magentoReview);
        $extendedReview->addData($extendedReviewData);

        $this->setIsCustomerVerified($extendedReview, $magentoReview);
    }

    /**
     * @param ReviewDetailsInterface $extendedReview
     * @param MagentoReview $review
     */
    private function setIsCustomerVerified(ReviewDetailsInterface $extendedReview, MagentoReview $review)
    {
        $isCustomerVerified = $this->getIsCustomerVerifiedByReview($review);
        $extendedReview->setCustomerVerified($isCustomerVerified);
    }

    /**
     * @param MagentoReview $magentoReview
     * @return bool
     */
    private function getIsCustomerVerifiedByReview(MagentoReview $magentoReview)
    {
        $customerId = $magentoReview->getData(ReviewDetailTableInterface::COLUMN_NAME_CUSTOMER_ID);
        $productId = $magentoReview->getEntityPkValue();

        return $this->isCustomerPurchaseProduct($customerId, $productId);
    }

    /**
     * Check has customer purchased reviewable product
     *
     * @param int|null $customerId
     * @param int $productId
     * @return bool
     */
    private function isCustomerPurchaseProduct($customerId, $productId)
    {
        if (!$customerId) {
            return false;
        }

        return $this->orderManagement->isCustomerPurchasedProduct($customerId, $productId);
    }

    /**
     * @param ReviewDetailsInterface $extendedReview
     * @throws CouldNotSaveException
     */
    private function saveExtendedReview(ReviewDetailsInterface $extendedReview)
    {
        $this->extendedReviewRepository->save($extendedReview);
    }

    /**
     * @param int $reviewId
     * @throws Exception
     */
    private function processImageSaving($reviewId)
    {
        $this->reviewImageHelper->processImageSaving($reviewId);
    }

    /**
     * @param MagentoReview $review
     * @param ReviewDetailsInterface $extendedReview
     * @param bool $isReviewExist
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    private function processEmailNotification(MagentoReview $review, ReviewDetailsInterface $extendedReview, $isReviewExist = false)
    {
        $this->notificationModel->processNotificationSending($review, $extendedReview, $isReviewExist);
    }

    /**
     * @param Exception $exception
     */
    private function addErrorMessage(Exception $exception)
    {
        $this->messageManager->addErrorMessage(__($exception->getMessage()));
    }
}
