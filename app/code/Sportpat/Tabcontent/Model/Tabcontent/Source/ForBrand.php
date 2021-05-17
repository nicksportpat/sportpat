<?php
namespace Sportpat\Tabcontent\Model\Tabcontent\Source;

use Magento\Framework\Option\ArrayInterface;

class ForBrand implements ArrayInterface
{
    const ALL_BRANDS = 1;
    const CKX = 2;
    const FXR_RACING = 3;
    const _509 = 4;
    const DRAGON = 5;

    /**
     * to option array
     *
     * @return array
     */

    protected $_eavAttribute;

    public function __construct(
        \Magento\Eav\Api\AttributeRepositoryInterface $eavAttribute
    ) {

        $this->_eavAttribute = $eavAttribute;

    }

    public function getAllBrandsForOptions() {

        $brandAttr = $this->_eavAttribute->get(\Magento\Catalog\Model\Product::ENTITY, 'brand');
        $allOptions = $brandAttr->getSource()->getAllOptions(false);
        $optionArray = [];
        foreach($allOptions as $option) {

            $optionArray[] = ['value' => $option["value"], 'label'=> $option["label"]];

        }

        return $optionArray;

    }

    public function toOptionArray()
    {
      /*  $options = [
            [
                'value' => self::ALL_BRANDS,
                'label' => __('All Brands')
            ],
            [
                'value' => self::CKX,
                'label' => __('CKX')
            ],
            [
                'value' => self::FXR_RACING,
                'label' => __('FXR RACING')
            ],
            [
                'value' => self::_509,
                'label' => __('509')
            ],
            [
                'value' => self::DRAGON,
                'label' => __('DRAGON')
            ],
        ];*/
        $options = $this->getAllBrandsForOptions();

        return $options;
    }
}
