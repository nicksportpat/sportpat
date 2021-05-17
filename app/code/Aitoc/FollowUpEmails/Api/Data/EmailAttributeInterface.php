<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Api\Data;

/**
 * Interface EmailAttributeInterface
 */
interface EmailAttributeInterface
{
    /**
     * Returns entity_id field
     *
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Returns email_id field
     *
     * @return int
     */
    public function getEmailId();

    /**
     * @param int $emailId
     *
     * @return $this
     */
    public function setEmailId($emailId);

    /**
     * Returns attribute_code field
     *
     * @return string
     */
    public function getAttributeCode();

    /**
     * @param string $attributeCode
     *
     * @return $this
     */
    public function setAttributeCode($attributeCode);

    /**
     * Returns value field
     *
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value);

    /**
     * @param array $arr
     * @return $this
     */
    public function addData(array $arr);
}
