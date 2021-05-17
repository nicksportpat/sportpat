<?php
/**
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Scommerce\GoogleUniversalAnalytics\Model\Source;

class Brand extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
	/**
     * @var \Scommerce\GoogleUniversalAnalytics\Helper\Data
     */
    protected $_guaData;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @param \Scommerce\GoogleUniversalAnalytics\Helper\Data $guaData
     */
    public function __construct(
        \Scommerce\GoogleUniversalAnalytics\Helper\Data $guaData,
        \Magento\Catalog\Model\Product $product
    ) {
        $this->_guaData = $guaData;
        $this->_product = $product;
    }
	
	public function getAllOptions()
	{
        $attributes = $this->_product->getAttributes();
        $attributeArray[] = array('label' => __('Please select'), 'value' => '');

        foreach($attributes as $attribute){
            $attributeArray[] = array(
                'label' => $attribute->getName(),
                'value' => $attribute->getName()
            );
        }
		
	    return $attributeArray;
	}
}