<?php

namespace Nick\Algoliasearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\CatalogRule\Model\Rule as RuleModel;

class PriceIndexObserver implements ObserverInterface
{

    protected $_ruleModel;
    protected $_productFactory;

    public function __construct(RuleModel $ruleModel,\Magento\Catalog\Model\ProductFactory $productFactory)
    {
        // Observer initialization code...
        // You can use dependency injection to get any class this observer may need.

        $this->_ruleModel = $ruleModel;
        $this->_productFactory = $productFactory;

    }

    public function execute(Observer $observer)
    {
        // Observer execution code...
        // Here you can modify frontend configuration

        // Example:
        // productsSettings = $observer->getData('index_settings');
        // $productsSettings['foo'] = 'bar';

        $product = $observer->getData('productObject');
        $productRecord = $observer->getData('custom_data');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $skus = [
"508342",
"508353",
"508354",
"508331",
"508332",
"508333",
"508334",
"508335",
"508336",
"508337",
"508361",
"508362",
"508363",
"508364",
"508365",
"508366",
"508367",
"508351",
"508352",
"508355",
"508356",
"508357",
"508341",
"508343",
"508344",
"508345",
"508346",
"508347",
"510673",
"510632",
"510635",
"510642",
"510645",
"510652",
"510655",
"510662",
"510665",
"510671",
"510674",
"510633",
"510636",
"510643",
"510646",
"510653",
"510656",
"510663",
"510666",
"510672",
"510675",
"510634",
"510637",
"510644",
"510647",
"510654",
"510657",
"510664",
"510667",
"510676",
"510677",
"510631",
"510641",
"510651",
"510661"
        ];


        $finalPrice = "";
        $pid = $product->getID();
        $ckxpercent = 27;
        $applyckx = false;

        if($product->getTypeId() == "configurable") {
            $configProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($pid);


            $_c = $configProduct->getTypeInstance()->getUsedProducts($configProduct);

            $childid = 0;
            $sku = "";

            foreach ($_c as $c) {



                if ($childid == 0) {
                    $childid = $c->getID();
                    $sku = $c->getSku();
                } else {
                    continue;
                }

            }

            if(in_array($sku, $skus)) {
                $sql = "SELECT * FROM catalogrule_product where product_id = ${childid} and sort_order = 0";
            } else {


                $sql = "SELECT * FROM catalogrule_product where product_id = ${childid}";
            }
            echo $sql . "\n";


            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();
            $result = $connection->fetchAll($sql);


            if (is_array($result) && isset($result[0])) {
                print_r($result[0]);
                $productRecord['price_info'] = [
                    "regular" => $result[0]["action_amount"],
                    "final" => $finalPrice,
                    "isSpecialPrice" => ""
                ];

            }
        } else {
            $productRecord['price_info'] = [
                "regular"=>1,
                "final" =>1,
                "isSpecialPrice"=>""
            ];
        }



        $regPrice = $product->getFinalPrice();



        if($regPrice == 0 && $product->getTypeId() == "configurable" || $productRecord['in_stock'] == 0) {
            $productRecord['in_stock'] = 0;
            $productRecord['visibility_search'] = 0;
            $productRecord['visibility_catalog'] = 0;

        }



    }

   public function checkIsSpecialPrice($price, $finalPrice) {

        $special = false;
        if($finalPrice !== NULL && $price != $finalPrice &&
        $price > $finalPrice) {
            $special = true;
        }

        return $special;


   }
}

