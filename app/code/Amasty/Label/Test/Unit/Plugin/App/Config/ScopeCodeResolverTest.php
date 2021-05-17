<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Test\Unit\Plugin\App\Config;

use Amasty\Label\Plugin\App\Config\ScopeCodeResolver;
use Amasty\Label\Test\Unit\Traits;

/**
 * Class ScopeCodeResolverTest
 *
 * @see ScopeCodeResolver
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class ScopeCodeResolverTest extends \PHPUnit\Framework\TestCase
{
    use Traits\ObjectManagerTrait;
    use Traits\ReflectionTrait;

    /**
     * @covers ScopeCodeResolver::beforeResolve
     */
    public function testBeforeResolve()
    {
        $plugin = $this->createPartialMock(ScopeCodeResolver::class, ['isNeedClean']);
        $resolver = $this->getObjectManager()->getObject(\Magento\Framework\App\Config\ScopeCodeResolver::class);

        $plugin->expects($this->any())->method('isNeedClean')->willReturnOnConsecutiveCalls(false, true);

        $this->setProperty($resolver, 'resolvedScopeCodes', ['test']);
        $plugin->beforeResolve($resolver, 'type', 'code');
        $this->assertEquals(['test'], $this->getProperty($resolver, 'resolvedScopeCodes'));
        $plugin->beforeResolve($resolver, 'type', 'code');
        $this->assertEquals([], $this->getProperty($resolver, 'resolvedScopeCodes'));
    }
}
