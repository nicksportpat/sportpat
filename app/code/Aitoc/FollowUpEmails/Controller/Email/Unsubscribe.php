<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Controller\Email;

use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\EmailRepositoryInterface;
use Aitoc\FollowUpEmails\Api\EventManagementInterface;
use Aitoc\FollowUpEmails\Api\Service\UnsubscribedEmailAddressInterface as UnsubscribedEmailAddressServiceInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class Unsubscribe
 */
class Unsubscribe extends Action
{
    const REQUEST_PARAM_NAME_EVENTS = 'events';
    const REQUEST_PARAM_NAME_UNSUBSCRIBE_CODE = 'code';

    /**
     * @var EmailRepositoryInterface
     */
    private $emailRepository;

    /**
     * @var EventManagementInterface
     */
    private $eventManagement;

    /**
     * @var UnsubscribedEmailAddressServiceInterface
     */
    private $unsubscribedEmailAddressService;

    /**
     * @param Context $context
     * @param EmailRepositoryInterface $emailRepository
     * @param EventManagementInterface $eventManagement
     * @param UnsubscribedEmailAddressServiceInterface $unsubscribedEmailAddressService
     */
    public function __construct(
        Context $context,
        EmailRepositoryInterface $emailRepository,
        EventManagementInterface $eventManagement,
        UnsubscribedEmailAddressServiceInterface $unsubscribedEmailAddressService
    ) {
        parent::__construct($context);

        $this->emailRepository = $emailRepository;
        $this->eventManagement = $eventManagement;
        $this->unsubscribedEmailAddressService = $unsubscribedEmailAddressService;
    }

    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $request = $this->getRequest();

        $eventsCodes = $this->getValidatedRequestedEventCodes($request);

        $requestedUnsubscribeCode = $this->getRequestedUnsubscribeCode($request);
        $email = $this->getEmailByUnsubscribeCode($requestedUnsubscribeCode);

        $customerEmail = $email->getEmailAddress();
        $emailId = $email->getEntityId();

        $this->updateUnsubscribedEventsForEmail($customerEmail, $eventsCodes, $emailId);
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    private function getValidatedRequestedEventCodes(RequestInterface $request)
    {
        $activeEventCodes = $this->getActiveEventsCodes();
        $requestedEventCodes = $this->getRequestedEventCodes($request);

        return array_intersect($activeEventCodes, $requestedEventCodes);
    }

    /**
     * @return string[]
     */
    private function getActiveEventsCodes()
    {
        return $this->eventManagement->getActiveEventsCodes();
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    private function getRequestedEventCodes(RequestInterface $request)
    {
        $checkedEventsCodes = $request->getParam(self::REQUEST_PARAM_NAME_EVENTS);

        return $checkedEventsCodes ? array_keys($checkedEventsCodes) : [];
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function getRequestedUnsubscribeCode(RequestInterface $request)
    {
        return $request->getParam(self::REQUEST_PARAM_NAME_UNSUBSCRIBE_CODE);
    }

    /**
     * @param string $unsubscribeCode
     * @return EmailInterface|null
     */
    private function getEmailByUnsubscribeCode($unsubscribeCode)
    {
        return $this->emailRepository->getByUnsubscribeCode($unsubscribeCode);
    }

    /**
     * @param int $emailId
     * @param string $customerEmail
     * @param string[] $eventsCodes
     */
    private function updateUnsubscribedEventsForEmail(
        $customerEmail,
        $eventsCodes,
        $emailId = null
    ) {
        $this->unsubscribedEmailAddressService->updateUnsubscribedEventsForEmail(
            $customerEmail,
            $eventsCodes,
            $emailId
        );
    }
}
