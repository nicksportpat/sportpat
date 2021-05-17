<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Plugin\App\Config;

/**
 * Class ScopeCodeResolver
 * @package Amasty\Label\Plugin\App\Config
 */
class ScopeCodeResolver
{
    /**
     * @var bool
     */
    private $needClean = false;

    /**
     * @param \Magento\Framework\App\Config\ScopeCodeResolver $scopeCodeResolver
     * @param string $scopeType
     * @param string $scopeCode
     *
     * @return array
     */
    public function beforeResolve(
        $scopeCodeResolver,
        $scopeType,
        $scopeCode
    ) {
        if ($this->isNeedClean() && method_exists($scopeCodeResolver, 'clean')) {
            $scopeCodeResolver->clean();
        }

        return [$scopeType, $scopeCode];
    }

    /**
     * @param bool $needClean
     */
    public function setNeedClean($needClean)
    {
        $this->needClean = $needClean;
    }

    /**
     * @return bool
     */
    public function isNeedClean()
    {
        return $this->needClean;
    }
}
