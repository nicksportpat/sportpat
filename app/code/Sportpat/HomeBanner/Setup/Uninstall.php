<?php
namespace Sportpat\HomeBanner\Setup;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * Uninstall constructor.
     * @param ResourceConnection $resource
     */
    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.Generic.CodeAnalysis.UnusedFunctionParameter)
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        //remove ui bookmark data
        $this->resource->getConnection()->delete(
            $this->resource->getTableName('ui_bookmark'),
            [
                'namespace IN (?)' => [
                    'sportpat_homebanner_banner_listing',
                ]
            ]
        );
        //remove config data
        $this->resource->getConnection()->delete(
            $this->resource->getTableName('core_config_data'),
            [
                'path LIKE ?' => 'sportpat_home_banner_%'
            ]
        );
        if ($setup->tableExists('sportpat_home_banner_banner_store')) {
            $setup->getConnection()->dropTableBannerStoreTable($setup);
        }
        if ($setup->tableExists('sportpat_home_banner_banner')) {
            $setup->getConnection()->dropTable('sportpat_home_banner_banner');
        }
    }
}
