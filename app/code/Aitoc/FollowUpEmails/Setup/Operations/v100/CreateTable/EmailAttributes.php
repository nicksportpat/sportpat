<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\FollowUpEmails\Setup\Operations\v100\CreateTable;

use Aitoc\FollowUpEmails\Api\Setup\V100\EmailAttributeTableInterface;
use Aitoc\FollowUpEmails\Api\Setup\V100\EmailTableInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class EmailAttributes
 */
class EmailAttributes
{
    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $emailsAttributesTable = $setup->getConnection()->newTable(
            $setup->getTable(EmailAttributeTableInterface::TABLE_NAME)
        )->addColumn(
            EmailAttributeTableInterface::COLUMN_NAME_ENTITY_ID,
            Table::TYPE_BIGINT,
            20,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity id'
        )->addColumn(
            EmailAttributeTableInterface::COLUMN_NAME_EMAIL_ID,
            Table::TYPE_BIGINT,
            20,
            ['unsigned' => true, 'nullable' => false],
            'Email id'
        )->addColumn(
            EmailAttributeTableInterface::COLUMN_NAME_ATTRIBUTE_CODE,
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true, 'default' => null],
            'Attribute code'
        )->addColumn(
            EmailAttributeTableInterface::COLUMN_NAME_VALUE,
            Table::TYPE_TEXT,
            255,
            ['unsigned' => false, 'nullable' => true, 'default' => null],
            'Value'
        )->addForeignKey(
            $setup->getFkName(
                EmailAttributeTableInterface::TABLE_NAME,
                EmailAttributeTableInterface::COLUMN_NAME_EMAIL_ID,
                EmailTableInterface::TABLE_NAME,
                EmailTableInterface::COLUMN_NAME_ENTITY_ID
            ),
            EmailAttributeTableInterface::COLUMN_NAME_EMAIL_ID,
            $setup->getTable(EmailTableInterface::TABLE_NAME),
            EmailTableInterface::COLUMN_NAME_ENTITY_ID,
            Table::ACTION_CASCADE
        )->setComment(
            'Email Attributes'
        );

        $setup->getConnection()->createTable($emailsAttributesTable);
    }
}
