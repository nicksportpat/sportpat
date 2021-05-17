<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Service;

use Aitoc\FollowUpEmails\Api\Data\UnsubscribedEmailAddressInterface as UnsubscribedEmailAddressDataInterface;
use Aitoc\FollowUpEmails\Api\Service\UnsubscribedEmailAddressInterface as UnsubscribedEmailAddressServiceInterface;
use Aitoc\FollowUpEmails\Api\UnsubscribedEmailAddressRepositoryInterface;

/**
 * Class UnsubscribedEmailAddress
 */
class UnsubscribedEmailAddress implements UnsubscribedEmailAddressServiceInterface
{
    /**
     * @var UnsubscribedEmailAddressRepositoryInterface
     */
    private $unsubscribedEmailAddressRepository;

    /**
     * UnsubscribedEmailAddress constructor.
     * @param UnsubscribedEmailAddressRepositoryInterface $unsubscribedEmailAddressRepository
     */
    public function __construct(UnsubscribedEmailAddressRepositoryInterface $unsubscribedEmailAddressRepository)
    {
        $this->unsubscribedEmailAddressRepository = $unsubscribedEmailAddressRepository;
    }

    /**
     * @param string $emailAddress
     * @param string[] $newUnsubscribedEventsCodes
     * @param int|null $emailId
     */
    public function updateUnsubscribedEventsForEmail($emailAddress, $newUnsubscribedEventsCodes, $emailId = null)
    {
        $unsubscribedEmailAddresses = $this->getUnsubscribedEmailAddresses($emailAddress);
        $oldUnsubscribedEventsCodes = $this->getEventCodesByUnsubscribedEmailAddresses($unsubscribedEmailAddresses);

        list($newEventsCodes, $deletedEventsCodes)
            = $this->sortEventsCodesByRequiredAction($oldUnsubscribedEventsCodes, $newUnsubscribedEventsCodes);

        if ($newEventsCodes) {
            $this->addUnsubscribedEmailAddressesByEventCodes($newEventsCodes, $emailAddress, $emailId);
        }

        if ($deletedEventsCodes) {
            $this->deleteUnsubscribedEmailAddressesByEventCodes($deletedEventsCodes, $unsubscribedEmailAddresses);
        }
    }

        /**
     * @param $emailAddress
     * @return UnsubscribedEmailAddressDataInterface[]
     */
    private function getUnsubscribedEmailAddresses($emailAddress)
    {
        return $this->unsubscribedEmailAddressRepository->getByEmailAddress($emailAddress);
    }

    /**
     * @param UnsubscribedEmailAddressDataInterface[] $unsubscribedEmailAddresses
     * @return string[]
     */
    private function getEventCodesByUnsubscribedEmailAddresses($unsubscribedEmailAddresses)
    {
        $eventCodes = [];

        foreach ($unsubscribedEmailAddresses as $unsubscribedEmailAddress) {
            $eventCodes[] = $unsubscribedEmailAddress->getEventCode();
        }

        return $eventCodes;
    }

    /**
     * @param string[] $oldEventsCodes
     * @param string[] $newEventsCodes
     * @return array[]
     */
    private function sortEventsCodesByRequiredAction($oldEventsCodes, $newEventsCodes)
    {
        $addEventsCodes = array_diff($newEventsCodes, $oldEventsCodes);
        $deleteEventCodes = array_diff($oldEventsCodes, $newEventsCodes);

        return [$addEventsCodes, $deleteEventCodes];
    }

    /**
     * @param string[] $eventsCodes
     * @param string $emailAddress
     * @param int|null $emailId
     */
    private function addUnsubscribedEmailAddressesByEventCodes($eventsCodes, $emailAddress, $emailId = null)
    {
        foreach ($eventsCodes as $eventCode) {
            $this->addUnsubscribedEmailAddress($emailAddress, $eventCode, $emailId);
        }
    }

    /**
     * @param string $emailAddress
     * @param string $eventCode
     * @param int|null $emailId
     */
    private function addUnsubscribedEmailAddress($emailAddress, $eventCode, $emailId = null)
    {
        $unsubscribedEmailAddress = $this->createUnsubscribedEmailAddress();
        $this->updateUnsubscribedEmailAddressByContext($unsubscribedEmailAddress, $emailAddress, $eventCode, $emailId);
        $this->saveUnsubscribedEmailAddress($unsubscribedEmailAddress);
    }

    /**
     * @return UnsubscribedEmailAddressDataInterface
     */
    private function createUnsubscribedEmailAddress()
    {
        return $this->unsubscribedEmailAddressRepository->create();
    }

    /**
     * @param UnsubscribedEmailAddressDataInterface $unsubscribedEmailAddress
     * @param string $emailAddress
     * @param string $eventCode
     * @param int|null $emailId
     */
    private function updateUnsubscribedEmailAddressByContext(
        UnsubscribedEmailAddressDataInterface $unsubscribedEmailAddress,
        $emailAddress,
        $eventCode,
        $emailId
    ) {
        $unsubscribedEmailAddress
            ->setCustomerEmail($emailAddress)
            ->setEventCode($eventCode)
            ->setEmailId($emailId)
        ;
    }

    /**
     * @param UnsubscribedEmailAddressDataInterface $unsubscribedEmailAddress
     */
    private function saveUnsubscribedEmailAddress(UnsubscribedEmailAddressDataInterface $unsubscribedEmailAddress)
    {
        $this->unsubscribedEmailAddressRepository->save($unsubscribedEmailAddress);
    }

    /**
     * @param string[] $eventCodes
     * @param UnsubscribedEmailAddressDataInterface[] $unsubscribedEmailAddresses
     */
    private function deleteUnsubscribedEmailAddressesByEventCodes($eventCodes, $unsubscribedEmailAddresses)
    {
        foreach ($unsubscribedEmailAddresses as $unsubscribedEmailAddress) {
            $this->deleteUnsubscribedEmailAddressesByEventCodeIfRequired($unsubscribedEmailAddress, $eventCodes);
        }
    }

    /**
     * @param UnsubscribedEmailAddressDataInterface $unsubscribedEmailAddress
     * @param string[] $eventCodes
     */
    private function deleteUnsubscribedEmailAddressesByEventCodeIfRequired(
        UnsubscribedEmailAddressDataInterface $unsubscribedEmailAddress,
        $eventCodes
    ) {
        if (!in_array($unsubscribedEmailAddress->getEventCode(), $eventCodes)) {
            return;
        }

        $this->deleteUnsubscribedEmailAddresses($unsubscribedEmailAddress);
    }

    /**
     * @param UnsubscribedEmailAddressDataInterface $unsubscribedEmailAddress
     */
    private function deleteUnsubscribedEmailAddresses(UnsubscribedEmailAddressDataInterface $unsubscribedEmailAddress)
    {
        $this->unsubscribedEmailAddressRepository->delete($unsubscribedEmailAddress);
    }
}
