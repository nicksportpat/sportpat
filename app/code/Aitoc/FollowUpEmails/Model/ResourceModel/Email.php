<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Model\ResourceModel;

use Aitoc\FollowUpEmails\Api\Data\EmailInterface;
use Aitoc\FollowUpEmails\Api\Setup\Current\EmailTableInterface;
use Exception;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Email
 */
class Email extends AbstractDb
{
    /**
     * @var string
     */
    protected $emailAttributesTable;

    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(EmailTableInterface::TABLE_NAME, EmailTableInterface::COLUMN_NAME_ENTITY_ID);
    }

    /**
     * Processing object after save data
     *
     * @param AbstractModel|EmailInterface $object
     */
    public function processAfterSaves(AbstractModel $object)
    {
        $emailId = $object->getEntityId();

        if ($emailAttributes = $object->getEmailAttributes()) {
            $this->saveEmailAttributesValues($emailId, $emailAttributes);
        }
    }

    /**
     * @param int $emailId
     * @param array $emailAttributes
     * @return $this
     */
    public function saveEmailAttributesValues($emailId, $emailAttributes)
    {
        $connection = $this->getConnection();
        $table = $this->getEmailAttributesTable();
        $existAttributes = $this->getEmailAttributesValues($emailId, $connection);

        foreach ($emailAttributes as $attributeCode => $value) {
            $select = $connection->select()
                ->from($table, ['attribute_code', 'value'])
                ->where('email_id = ?', $emailId)
                ->where('attribute_code = ?', $attributeCode);

            $result = $connection->fetchCol($select);

            if ($result) {
                $this->getConnection()->update(
                    $table,
                    ['value' => $value],
                    ['email_id IN(?)' => $emailId, 'attribute_code IN(?)' => $attributeCode]
                );
            } else {
                $this->getConnection()->insert(
                    $table,
                    ['email_id' => $emailId, 'attribute_code' => $attributeCode, 'value' => $value]
                );
            }
        }

        $emailAttributes = array_keys($emailAttributes);
        $extraAttributes = array_diff($existAttributes, $emailAttributes);
        $this->deleteEmailAttributesValues($emailId, $extraAttributes);

        return $this;
    }

    /**
     * @return string
     */
    public function getEmailAttributesTable()
    {
        if (!$this->emailAttributesTable) {
            $this->emailAttributesTable = $this->getTable(EmailAttribute::TABLE);
        }
        return $this->emailAttributesTable;
    }

    /**
     * @param int $emailId
     * @param AdapterInterface $dbAdapter
     * @return array
     */
    public function getEmailAttributesValues($emailId, $dbAdapter)
    {
        $select = $dbAdapter
            ->select()
            ->from($this->getEmailAttributesTable(), ['attribute_code', 'value'])
            ->where('email_id = ?', $emailId)
        ;

        $result = $dbAdapter->fetchCol($select);

        return $result;
    }

    /**
     * @param int $emailId
     * @param array $extraAttributes
     * @return $this
     */
    public function deleteEmailAttributesValues($emailId, $extraAttributes)
    {
        $this->getConnection()->delete(
            $this->getEmailAttributesTable(),
            ['email_id IN(?)' => $emailId, 'attribute_code IN(?)' => $extraAttributes]
        );

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return AbstractDb
     * @throws Exception
     */
    public function delete(AbstractModel $object)
    {
        $emailId = $object->getEntityId();
        $this->getConnection()->delete(
            $this->getEmailAttributesTable(),
            ['email_id IN(?)' => $emailId]
        );

        return parent::delete($object);
    }
}
