<?php
namespace Sportpat\Tabcontent\Model\Tabcontent;

use Sportpat\Tabcontent\Ui\Provider\CollectionProviderInterface;
use Magento\Ui\Component\MassAction\Filter;
use Sportpat\Tabcontent\Model\ResourceModel\Tabcontent\CollectionFactory;

class CollectionProvider implements CollectionProviderInterface
{
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * CollectionRetriever constructor.
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function getCollection()
    {
        return $this->filter->getCollection($this->collectionFactory->create());
    }
}
