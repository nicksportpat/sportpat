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

use Aitoc\FollowUpEmails\Api\Helper\EmailInterface as EmailHelperInterface;
use Aitoc\FollowUpEmails\Api\Helper\EventEmailsGeneratorHelperInterface;

/**
 * Class Email
 */
class Email implements EmailHelperInterface
{
    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param array $emailAttributes
     * @return int
     */
    public function getStoreIdByEmailAttributes(
        EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper,
        $emailAttributes
    ) {
        $entity = $this->getEntityByEmailAttributes($eventEmailsGeneratorHelper, $emailAttributes);

        return $eventEmailsGeneratorHelper->getStoreIdByEntity($entity);
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param array $emailAttributes
     * @return mixed
     */
    public function getEntityByEmailAttributes(EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper, $emailAttributes)
    {
        $entityId  = $this->getEntityIdByAttributes($eventEmailsGeneratorHelper, $emailAttributes);

        return $eventEmailsGeneratorHelper->getEntityById($entityId);
    }

    /**
     * @param EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper
     * @param array $emailAttributes
     * @return int
     */
    private function getEntityIdByAttributes(EventEmailsGeneratorHelperInterface $eventEmailsGeneratorHelper, $emailAttributes)
    {
        $entityAttributeCode = $eventEmailsGeneratorHelper->getEntityIdAttributeCode();

        return $emailAttributes[$entityAttributeCode];
    }
}
