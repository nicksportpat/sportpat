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

use Aitoc\FollowUpEmails\Api\Helper\EmailInterface as EmailHelperInterface;
use Aitoc\FollowUpEmails\Helper\BaseEventEmailsGeneratorHelper;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\DateTime as DateTimeHelper;

/**
 * Class Customer
 */
abstract class Customer extends BaseEventEmailsGeneratorHelper
{
    const ATTRIBUTE_CODE_CUSTOMER_ID = 'customer_id';

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * Customer constructor.
     * @param EmailHelperInterface $emailHelper
     * @param DateTimeHelper $dateTimeHelper
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        EmailHelperInterface $emailHelper,
        DateTimeHelper $dateTimeHelper,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($emailHelper, $dateTimeHelper);

        $this->customerRepository = $customerRepository;
    }

    /**
     * @param CustomerInterface $entity
     * @return int
     */
    public function getEntityIdByEntity($entity)
    {
        return $entity->getId();
    }

    /**
     * To get Customer full name for email.
     *
     * @param CustomerInterface $entity
     * @return string|null
     */
    public function getCustomerFirstsNameByEntity($entity)
    {
        return $entity->getFirstname();
    }

    /**
     * To get Customer full name for email.
     *
     * @param CustomerInterface $entity
     * @return string|null
     */
    public function getCustomerLastNameByEntity($entity)
    {
        return $entity->getLastname();
    }

    /**
     * @return string
     */
    public function getEntityIdAttributeCode()
    {
        return self::ATTRIBUTE_CODE_CUSTOMER_ID;
    }

    /**
     * @param CustomerInterface $entity
     * @return string
     */
    public function getCustomerEmailByEntity($entity)
    {
        return $entity->getEmail();
    }

    /**
     * @param CustomerInterface $entity
     * @return int
     */
    public function getStoreIdByEntity($entity)
    {
        return $entity->getStoreId();
    }

    /**
     * @param int $entityId
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getEntityById($entityId)
    {
        return $this->getCustomerById($entityId);
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
