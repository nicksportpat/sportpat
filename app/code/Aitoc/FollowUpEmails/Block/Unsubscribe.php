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

use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressInterface;
use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressSearchResultsInterface;
use Aitoc\FollowUpEmails\Api\EmailRepositoryInterface;
use Aitoc\FollowUpEmails\Api\EventManagementInterface;
use Aitoc\FollowUpEmails\Api\Helper\SearchCriteriaBuilderInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\UnsubscribedEmailAddressTableInterface;
use Aitoc\FollowUpEmails\Api\UnsubscribedEmailAddressRepositoryInterface;
use InvalidArgumentException;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aitoc\FollowUpEmails\Helper\Url;

/**
 * Class Unsubscribe
 */
class Unsubscribe extends Template
{
    const REQUEST_PARAM_NAME_UNSUBSCRIBE_CODE = 'code';

    const ERROR_MESSAGE_INVALID_UNSUBSCRIBE_CODE = 'Invalid unsubscribe code "%s"';

    const ROUTE_PATH_UNSUBSCRIBE = 'followup/email/unsubscribe';

    /**
     * @var EventManagementInterface
     */
    private $eventManagement;

    /**
     * @var EmailRepositoryInterface
     */
    private $emailsRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var UnsubscribedEmailAddressRepositoryInterface
     */
    private $unsubscribedListRepository;

    /**
     * @var SearchCriteriaBuilderInterface
     */
    private $searchCriteriaBuilderHelper;

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
        EventManagementInterface $eventManagement,
        EmailRepositoryInterface $emailsRepository,
        SearchCriteriaBuilderInterface $searchCriteriaBuilderHelper,
        FilterBuilder $filterBuilder,
        UnsubscribedEmailAddressRepositoryInterface $unsubscribedListRepository,
        UrlInterface $urlBuilder,
        Url $url
    ) {
        parent::__construct($context);

        $this->eventManagement = $eventManagement;
        $this->emailsRepository = $emailsRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilderHelper = $searchCriteriaBuilderHelper;
        $this->unsubscribedListRepository = $unsubscribedListRepository;
        $this->urlBuilder = $urlBuilder;
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getUnsubscribedEventsCodes()
    {
        $unsubscribeCode = $this->getUnsubscribeCode();

        $email = $this->getEmailByUnsubscribeCodeOrThrow($unsubscribeCode);
        $customerEmail = $email->getEmailAddress();

        return $this->getUnsubscribedEventCodesByCustomerEmail($customerEmail);
    }

    /**
     * @return string
     */
    public function getUnsubscribeCode()
    {
        $request = $this->getRequest();

        return $this->getRequestedUnsubscribeCode($request);
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
     * @return EmailInterface
     */
    private function getEmailByUnsubscribeCodeOrThrow($unsubscribeCode)
    {
        $email = $this->getEmailByUnsubscribeCode($unsubscribeCode);

        //todo: what show for invalid unsubscribe code?
        if (!$email) {
            $errorMessage = sprintf(self::ERROR_MESSAGE_INVALID_UNSUBSCRIBE_CODE, $unsubscribeCode);

            throw new InvalidArgumentException($errorMessage);
        }

        return $email;
    }

    /**
     * @param $unsubscribeCode
     * @return EmailInterface|null
     */
    private function getEmailByUnsubscribeCode($unsubscribeCode)
    {
        $filters = [
            [EmailTableInterface::COLUMN_NAME_SECRET_CODE, $unsubscribeCode]
        ];

        $searchCriteria = $this->searchCriteriaBuilderHelper->createSearchCriteria($filters);

        $emailsList = $this->emailsRepository->getList($searchCriteria);

        $emails = $emailsList->getItems();

        return $emails ? reset($emails) : null;
    }

    /**
     * @param $customerEmail
     * @return array
     */
    private function getUnsubscribedEventCodesByCustomerEmail($customerEmail)
    {
        $unsubscribedEmailAddresses = $this->getUnsubscribedEmailAddressesByCustomerEmail($customerEmail);

        $unsubscribedEventCodes = [];

        foreach ($unsubscribedEmailAddresses as $unsubscribedEmailAddress) {
            $unsubscribedEventCodes[] = $unsubscribedEmailAddress->getEventCode();
        }

        return $unsubscribedEventCodes;
    }

    /**
     * @param $customerEmail
     * @return UnsubscribedEmailAddressInterface[]
     */
    private function getUnsubscribedEmailAddressesByCustomerEmail($customerEmail)
    {
        $filters = [
            [UnsubscribedEmailAddressTableInterface::COLUMN_NAME_CUSTOMER_EMAIL, $customerEmail]
        ];

        $searchCriteria = $this->searchCriteriaBuilderHelper->createSearchCriteria($filters);

        return $this->getUnsubscribedEmailAddressesBySearchCriteria($searchCriteria);
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return UnsubscribedEmailAddressInterface[]
     */
    private function getUnsubscribedEmailAddressesBySearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        $unsubscribedEmailAddressSearchResults = $this->getUnsubscribedEmailAddressSearchResultsBySearchCriteria($searchCriteria);

        return $unsubscribedEmailAddressSearchResults->getItems();
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return UnsubscribedEmailAddressSearchResultsInterface
     */
    private function getUnsubscribedEmailAddressSearchResultsBySearchCriteria(SearchCriteriaInterface $searchCriteria)
    {
        return $this->unsubscribedListRepository->getList($searchCriteria);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return [
            'url' => $this->getUnsubscribeUrl(),
            'code' => $this->getUnsubscribeCode(),
        ];
    }

    /**
     * @return string
     */
    public function getUnsubscribeUrl()
    {
        return $this->getUrl(self::ROUTE_PATH_UNSUBSCRIBE);
    }

    /**
     * @return array
     */
    public function getActiveEvents()
    {
        return $this->eventManagement->getActiveEvents();
    }

    /**
     * @return string[]
     */
    public function getActiveEventsCodes()
    {
        return $this->eventManagement->getActiveEventsCodes();
    }
}
