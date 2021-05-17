<?php
/**
 * A Magento 2 module named Ncloutier/Shippingsentence
 * Copyright (C) 2017  
 * 
 * This file is part of Ncloutier/Shippingsentence.
 * 
 * Ncloutier/Shippingsentence is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Ncloutier\Shippingsentence\Block\Html;

class Sentences  extends \Magento\Framework\View\Element\Template
{

    protected $_registry;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,


        array $data = []
    ) {


        $this->_registry = $registry;

        parent::__construct($context, $data);



    }

    /**
     * @return string
     */
    public function showSentence()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
$productid = $this->getCurrentProduct()->getId();
        $connection = $resource->getConnection();
        $sql = "SELECT * FROM `amasty_multiinventory_warehouse_item` WHERE `product_id` = '".$productid."'";
        $rows = $connection->fetchAll($sql);

        var_dump($rows);

        //Your block code
        return '';
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }
}
