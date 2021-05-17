<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Label
 */


namespace Amasty\Label\Model\Indexer;

/**
 * Class CacheContext
 * @package Amasty\Label\Model\Indexer
 */
class CacheContext extends \Magento\Framework\Indexer\CacheContext
{
    /**
     * Register entity Ids
     *
     * @param string $cacheTag
     * @param array $ids
     * @return $this
     */
    public function registerEntities($cacheTag, $ids)
    {
        $this->entities[$cacheTag] = $ids;

        return $this;
    }
}
