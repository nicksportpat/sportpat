<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Helper;

use Aitoc\FollowUpEmails\Api\Data\CampaignStepInterface;
use Aitoc\FollowUpEmails\Api\Helper\EmailInterface as EmailHelperInterface;
use Aitoc\FollowUpEmails\Api\Helper\EventEmailsGeneratorHelperInterface;
use Magento\Framework\Stdlib\DateTime\DateTime as DateTimeHelper;

/**
 * Class BaseEventEmailsGeneratorHelper
 */
abstract class BaseEventEmailsGeneratorHelper implements EventEmailsGeneratorHelperInterface
{
    /**
     * @var EmailHelperInterface
     */
    protected $emailHelper;

    /**
     * @var DateTimeHelper
     */
    protected $dateTimeHelper;

    /**
     * BaseEventEmailsGeneratorHelper constructor.
     * @param EmailHelperInterface $emailHelper
     * @param DateTimeHelper $dateTimeHelperTimeHelper
     */
    public function __construct(
        EmailHelperInterface $emailHelper,
        DateTimeHelper $dateTimeHelperTimeHelper
    ) {
        $this->emailHelper = $emailHelper;
        $this->dateTimeHelper = $dateTimeHelperTimeHelper;
    }

    /**
     * @param CampaignStepInterface $campaignStep
     * @return array
     */
    public function getAdditionalEmailAddresses(CampaignStepInterface $campaignStep)
    {
        return [];
    }

    /**
     * @param array $emailAttributes
     * @return mixed
     */
    protected function getEntityByEmailAttributes($emailAttributes)
    {
        return $this->emailHelper->getEntityByEmailAttributes($this, $emailAttributes);
    }

    /**
     * @param array $emailAttributes
     * @return int
     */
    protected function getEntityIdByEmailAttributes($emailAttributes)
    {
        $entityAttributeCode = $this->getEntityIdAttributeCode();

        return $emailAttributes[$entityAttributeCode];
    }

    /**
     * @param CampaignStepInterface $campaignStep
     * @return bool
     */
    public function isSendEmailToCustomerRequired(CampaignStepInterface $campaignStep)
    {
        return true;
    }

    /**
     * @param mixed $entity
     * @return array
     */
    public function getEmailAttributesByEntity($entity)
    {
        return [
            $this->getEntityIdAttributeCode() => $this->getEntityIdByEntity($entity),
        ];
    }

    /**
     * @param $string
     * @return int
     */
    protected function convertToTimestamp($string)
    {
        return $this->dateTimeHelper->timestamp($string);
    }
}
