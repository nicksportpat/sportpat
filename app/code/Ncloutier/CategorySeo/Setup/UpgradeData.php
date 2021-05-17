<?php

namespace Ncloutier\CategorySeo\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
  public $_eavSetup;
  public $eavSetupFactory;

    public function __construct
    (
        \Magento\Eav\Setup\EavSetup $eavSetup,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
    )
    {
        $this->_eavSetup = $eavSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }
    public function upgrade(ModuleDataSetupInterface $setup,ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.3') < 0) {

            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->removeAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'seo_text');

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'seo_text',
                [
                    'type' => 'text',
                    'label' => 'Seo text',
                    'input' => 'textarea',
                    'sort_order' => 333,
                    'source' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => null,
                    'group' => 'General Information',
                    'backend' => '',
                    'wysiwyg_enabled' => true,
                ]
            );

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'seo_text_en',
                [
                    'type' => 'text',
                    'label' => 'Seo text English',
                    'input' => 'textarea',
                    'sort_order' => 333,
                    'source' => '',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => null,
                    'group' => 'General Information',
                    'backend' => '',
                    'wysiwyg_enabled' => true,
                ]
            );

        }

        $setup->endSetup();
    }

}