<?php
namespace Sportpat\OrderSync\Ui\Provider;

interface CollectionProviderInterface
{
    /**
     * @return \Sportpat\OrderSync\Model\ResourceModel\AbstractCollection
     */
    public function getCollection();
}
