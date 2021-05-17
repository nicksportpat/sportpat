<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Setup\Base;

use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class TransactionSetup
 */
class TransactionSetup
{
    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function beginTransaction(ModuleDataSetupInterface $setup)
    {
        $dbAdapter = $setup->getConnection();
        $dbAdapter->beginTransaction();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function commitTransaction(ModuleDataSetupInterface $setup)
    {
        $dbAdapter = $setup->getConnection();
        $dbAdapter->commit();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     */
    protected function rollBackTransaction(ModuleDataSetupInterface $setup)
    {
        $dbAdapter = $setup->getConnection();
        $dbAdapter->rollBack();
    }
}
