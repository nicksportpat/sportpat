<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */


namespace Aitoc\FollowUpEmails\Setup\Base;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql as MysqlAdapter;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class RenameColumns
 */
abstract class RenameColumns implements UpgradeSchemaOperationInterface
{
    /**
     * @return string
     */
    abstract protected function getTableName();

    /**
     * @return array [$fromColumn1 => $toColumn1, ...]
     */
    abstract protected function getRenameColumnsMap();

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $tableName = $this->getTableName();
        $prefixedTableName = $this->getPrefixedTableName($setup, $tableName);
        $tableDescription = $this->getTableDescription($setup, $prefixedTableName);

        $renameColumnsMap = $this->getRenameColumnsMap();

        foreach ($renameColumnsMap as $from => $to) {
            $this->renameColumn($setup, $tableDescription, $prefixedTableName, $from, $to);
        }
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param string $tableName
     * @return string
     */
    private function getPrefixedTableName(SchemaSetupInterface $setup, $tableName)
    {
        return $setup->getTable($tableName);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param array $tableDescription
     * @param string $prefixedTableName
     * @param string $fromColumnName
     * @param string $toColumnName
     * @throws Zend_Db_Exception
     */
    protected function renameColumn(SchemaSetupInterface $setup, $tableDescription, $prefixedTableName, $fromColumnName, $toColumnName)
    {
        /** @var MysqlAdapter $dbAdapter */
        $dbAdapter = $setup->getConnection();

        $fromColumnDefinition = $this->getColumnDefinition($dbAdapter, $tableDescription, $fromColumnName);
        $toColumnDefinition = $this->getToColumnDefinition($fromColumnDefinition, $toColumnName);

        $dbAdapter->changeColumn($prefixedTableName, $fromColumnName, $toColumnName, $toColumnDefinition);
    }

    /**
     * @param array $fromColumnDefinition
     * @return array
     */
    protected function getToColumnDefinition($fromColumnDefinition, $toColumnName)
    {
        return $fromColumnDefinition;
    }

    /**
     * @param AdapterInterface|MysqlAdapter $dbAdapter
     * @param array $tableDescription
     * @param string $columnName
     * @return array
     */
    private function getColumnDefinition(AdapterInterface $dbAdapter, $tableDescription, $columnName)
    {
        $columnDescription = $this->getColumnDescription($tableDescription, $columnName);

        return $dbAdapter->getColumnCreateByDescribe($columnDescription);
    }

    /**
     * @param array $tableDescription
     * @param string $columnName
     * @return array
     */
    private function getColumnDescription($tableDescription, $columnName)
    {
        return $tableDescription[$columnName];
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param $prefixedTableName
     * @return array
     */
    private function getTableDescription(SchemaSetupInterface $setup, $prefixedTableName)
    {
        $connection = $setup->getConnection();

        return $connection->describeTable($prefixedTableName);
    }
}
