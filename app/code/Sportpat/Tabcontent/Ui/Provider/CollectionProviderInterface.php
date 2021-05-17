<?php
namespace Sportpat\Tabcontent\Ui\Provider;

interface CollectionProviderInterface
{
    /**
     * @return \Sportpat\Tabcontent\Model\ResourceModel\AbstractCollection
     */
    public function getCollection();
}
