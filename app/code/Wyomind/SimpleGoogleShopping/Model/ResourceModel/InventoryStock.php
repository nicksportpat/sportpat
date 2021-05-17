<?php

/**
 * Copyright © 2015 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\SimpleGoogleShopping\Model\ResourceModel;


/**
 * Class InventoryStock
 * @package Wyomind\SimpleGoogleShopping\Model\ResourceModel
 */
class InventoryStock extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var null|string
     */
    private $inventoryReservationTable=null;
    /**
     * @var string
     */
    private $catalogProductEntityTable;



    /**
     * InventoryStock constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param null $connectionName
     */


    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $connectionName=null
    )
    {


        parent::__construct($context, $connectionName);

        $stockSourceLinkCollection=$objectManager->create("\Magento\Inventory\Model\ResourceModel\StockSourceLink\Collection");

        foreach ($stockSourceLinkCollection as $stock) {
            $this->stocks[$stock->getStockId()][]=$stock->getSourceCode();
        }
        $this->inventoryReservationTable=$this->getTable("inventory_reservation");
        $this->catalogProductEntityTable=$this->getTable("catalog_product_entity");
    }

    /**
     * Resource initialization
     * @return void
     */
    public function _construct()
    {
        $this->_init('simplegoogleshopping_feeds', 'id');
    }

    /**
     * Collect all data related to the stock inventory other that the default stock
     * @return array
     */
    public function collect()
    {
        $stocks=[];
        foreach ($this->stocks as $stockId=>$stock) {
            if ($stockId == 1) {
                continue;
            }

            $inventoryStockTable=$this->getTable("inventory_stock_" . $stockId);

            $select=$this->getConnection()->select();


            $select->from(["inventory_stock"=>$inventoryStockTable])->reset('columns')
                ->columns(["cpe.entity_id", new \Zend_Db_Expr("inventory_stock.quantity+IF(ISNULL(SUM(ir.quantity)),0,SUM(ir.quantity)) AS quantity"), new \Zend_Db_Expr("MAX(is_salable) as is_salable")])
                ->joinLeft(["ir"=>$this->inventoryReservationTable], "ir.sku = inventory_stock.sku", [])
                ->joinLeft(["cpe"=>$this->catalogProductEntityTable], "inventory_stock.sku = cpe.sku", [])
                ->group(["cpe.entity_id"]);


            $data=$this->getConnection()->fetchAll($select);
            foreach ($data as $product) {
                $stocks[$stockId][$product["entity_id"]]=array(
                    "quantity"=>$product["quantity"],
                    "is_salable"=>$product["is_salable"],
                );
            }


        }


        return $stocks;

    }
}