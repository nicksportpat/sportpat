<?php
namespace Sportpat\Tabcontent\Model\Tabcontent\Source;

use Magento\Framework\Option\ArrayInterface;

class ForCategory implements ArrayInterface
{
    const ALL_CATEGORIES = 1;
    const SNOWMOBILE = 2;
    const SNOWMOBILE_JACKETS = 3;
    const SNOWMOBILE_HELMETS = 4;
    const SNOWMOBILE_MONOSUITS = 5;
    const SNOWMOBILE_HEADWEARS = 6;

    protected $_collection;



    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collection
    ) {
        $this->_collection = $collection;
    }

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_collection->create();
        $collection->addAttributeToSelect('*')->addFieldToFilter('is_active', 1);
        $itemArray = array('value' => '', 'label' => '--Please Select--');
        $options = [];
        $options = $itemArray;
        foreach ($collection as $category) {
            $options[] = ['value' => $category->getId(), 'label' => $category->getName()];
        }
        return $options;

    }
}
