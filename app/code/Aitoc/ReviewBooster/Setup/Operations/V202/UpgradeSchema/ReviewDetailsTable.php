<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Setup\Operations\V202\UpgradeSchema;

use Aitoc\FollowUpEmails\Api\Setup\UpgradeSchemaOperationInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Aitoc\ReviewBooster\Setup\Operations\V202\UpgradeSchema\ReviewDetailsTable\RenameColumns as RenameColumnsOperation;
use Zend_Db_Exception;

/**
 * Class ReviewDetailsTable
 */
class ReviewDetailsTable implements UpgradeSchemaOperationInterface
{
    private $renameColumnsOperation;

    /**
     * ReviewDetailsTable constructor.
     * @param RenameColumnsOperation $renameColumnsOperation
     */
    public function __construct(RenameColumnsOperation $renameColumnsOperation)
    {
        $this->renameColumnsOperation = $renameColumnsOperation;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $this->renameColumns($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function renameColumns(SchemaSetupInterface $setup)
    {
        $this->renameColumnsOperation->execute($setup);
    }
}
