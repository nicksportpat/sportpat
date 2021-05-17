<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\ReviewBooster\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Registry;

class RegistryDataProvider
{
    const KEY_CURRENT_PRODUCT = 'current_product';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * RegistryDataProvider constructor.
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return ProductInterface
     */
    public function getCurrentProduct()
    {
        return $this->getRegistryValue(self::KEY_CURRENT_PRODUCT);
    }

    /**
     * @param string $key
     * @return mixed
     */
    private function getRegistryValue($key)
    {
        return $this->registry->registry($key);
    }
}