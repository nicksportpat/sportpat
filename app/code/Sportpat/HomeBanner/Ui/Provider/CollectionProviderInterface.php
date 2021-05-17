<?php
namespace Sportpat\HomeBanner\Ui\Provider;

interface CollectionProviderInterface
{
    /**
     * @return \Sportpat\HomeBanner\Model\ResourceModel\AbstractCollection
     */
    public function getCollection();
}
