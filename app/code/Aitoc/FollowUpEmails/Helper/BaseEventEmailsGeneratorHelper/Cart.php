<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper\BaseEventEmailsGeneratorHelper;

use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Helper\EmailInterface as EmailHelperInterface;
use Aitoc\FollowUpEmails\Helper\BaseEventEmailsGeneratorHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Framework\Stdlib\DateTime\DateTime as DateTimeHelper;

/**
 * Class Cart
 */
abstract class Cart extends BaseEventEmailsGeneratorHelper
{
    const EMAIL_ATTRIBUTE_CODE_QUOTE_ID = 'quote_id';

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Cart constructor.
     * @param EmailHelperInterface $emailHelper
     * @param CartRepositoryInterface $orderRepository
     * @param DateTimeHelper $dateTimeHelperTimeHelper
     */
    public function __construct(
        EmailHelperInterface $emailHelper,
        CartRepositoryInterface $orderRepository,
        DateTimeHelper $dateTimeHelperTimeHelper
    ) {
        parent::__construct($emailHelper, $dateTimeHelperTimeHelper);
        $this->quoteRepository = $orderRepository;
    }

    /**
     * @param CartInterface $entity
     * @return int
     */
    public function getEntityIdByEntity($entity)
    {
        return $entity->getId();
    }

    /**
     * @inheritdoc
     */
    public function getEntityIdAttributeCode()
    {
        return self::EMAIL_ATTRIBUTE_CODE_QUOTE_ID;
    }

    /**
     * @param int $entityId
     * @return CartInterface
     * @throws NoSuchEntityException
     */
    public function getEntityById($entityId)
    {
        return $this->getQuoteById($entityId);
    }

    /**
     * @param CartInterface $entity
     * @return mixed
     */
    public function getEventTimestampByEntity($entity)
    {
        $updatedAt = $entity->getUpdatedAt();

        return $this->convertToTimestamp($updatedAt);
    }

    /**
     * @param CartInterface $entity
     * @return string|null
     */
    public function getCustomerFirstsNameByEntity($entity)
    {
        return ($customer = $entity->getCustomer())
            ? $customer->getFirstname()
            : $entity->getBillingAddress()->getFirstname();
    }

    /**
     * @param CartInterface $entity
     * @return string|null
     */
    public function getCustomerLastNameByEntity($entity)
    {
        return ($customer = $entity->getCustomer())
            ? $customer->getLastname()
            : $entity->getBillingAddress()->getLastname();
    }

    /**
     * @param CartInterface|Quote $entity
     * @return string
     */
    public function getCustomerEmailByEntity($entity)
    {
        return $entity->getCustomerEmail();
    }

    /**
     * @param $quoteId
     * @return CartInterface
     * @throws NoSuchEntityException
     */
    protected function getQuoteById($quoteId)
    {
        return $this->quoteRepository->get($quoteId);
    }

    /**
     * @param CartInterface $entity
     * @return int
     */
    public function getStoreIdByEntity($entity)
    {
        return $entity->getStoreId();
    }

    /**
     * @param CampaignStepInterface $campaignStep
     * @param array $emailAttributes
     * @return bool
     */
    public function canSendEmail(CampaignStepInterface $campaignStep, $emailAttributes)
    {
        $quoteModel = $this->getEntityByEmailAttributes($emailAttributes);

        return (bool) $quoteModel->getIsActive();
    }
}
