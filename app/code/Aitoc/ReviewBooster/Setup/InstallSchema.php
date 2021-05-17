<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Setup;

use Aitoc\ReviewBooster\Setup\Operations\V100\InstallSchema\CreateReminderTable as CreateReminderTableOperation;
use Aitoc\ReviewBooster\Setup\Operations\V100\InstallSchema\CreateReviewDetailsTable as CreateReviewDetailTableOperation;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var CreateReviewDetailTableOperation
     */
    private $createReviewDetailTableOperation;

    /**
     * @var CreateReminderTableOperation
     */
    private $createReminderTableOperation;

    /**
     * InstallSchema constructor.
     * @param CreateReviewDetailTableOperation $createReviewDetailTableOperation
     * @param CreateReminderTableOperation $createReminderTableOperation
     */
    public function __construct(
        CreateReviewDetailTableOperation $createReviewDetailTableOperation,
        CreateReminderTableOperation $createReminderTableOperation
    ) {
        $this->createReviewDetailTableOperation = $createReviewDetailTableOperation;
        $this->createReminderTableOperation = $createReminderTableOperation;
    }


    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->createReviewDetailsTable($setup);
        $this->createReminderTable($setup);
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws Zend_Db_Exception
     */
    private function createReviewDetailsTable(SchemaSetupInterface $setup)
    {
        $this->createReviewDetailTableOperation->execute($setup);
    }

    /**
     * @param $setup
     * @throws Zend_Db_Exception
     */
    private function createReminderTable(SchemaSetupInterface $setup)
    {
        $this->createReminderTableOperation->execute($setup);
    }
}
