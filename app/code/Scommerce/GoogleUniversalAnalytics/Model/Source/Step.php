<?php
/**
 * Copyright © 2015 Scommerce Mage. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Scommerce\GoogleUniversalAnalytics\Model\Source;

class Step extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
	/**
	 * return the list of surcharge type for administrator to choose from
	 */
	public function getAllOptions()
	{
		return array(
			array('value' => "1", 'label'=>__('Billing and Shipping information')),
			array('value' => "2", 'label'=>__('Payment and Review Information')),
		);
	}
}