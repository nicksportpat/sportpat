<?php
namespace Ncloutier\Shippingsentence\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_request;
    protected $_productData;

    public function __construct(

        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Action\Context $context)
    {
        $obj = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_productData = $obj->get('Magento\Catalog\Model\Product');
        $this->_request = $request;
        return parent::__construct($context);
    }

    public function execute()
    {
        $postData = $this->_request->getParams();

        if (isset($postData["pid"])) {

            $productid = $postData["pid"];
            $this->_productData = $this->_productData->load($productid);

 $sentence = "";

            $dataarr = [];
            $dataarr["sentence"] = $sentence;
            $dataarr["sku"] = $this->getSku();
            $dataarr["title"] = $this->getTitle();
            $dataarr["color"] = $this->getColor();
            $dataarr["size"] = $this->getSize();
            echo json_encode($dataarr);

            return;

$vqty = true;

           $lsqty = $this->getLSInventory($productid);
            if($lsqty == 0) {
               $vqty = $this->getVendorInventory($productid);
                if($vqty !== false) {
                    $wid = "";
                    switch ($vqty["warehouse_id"]) {
                        case "4":
                            $wid = "kimpex";
                            break;
                        case "5":
                            $wid = "thibault";
                            break;
                        case "6":
                            $wid = "partscanada";
                            break;
                        case "7":
                            $wid = "gamma";
                            break;
                        case "8":
                            $wid = "motovan";
                            break;
                        case "9":
                            $wid = "canadacurrent";
                            break;
                        case "10":
                            $wid = "fox";
                            break;
                        case "11":
                            $wid = "msd";

                    }
                   $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                    $connection = $resource->getConnection();
                    $query = "SELECT * FROM `ncloutier_shippingsentence_sentences` WHERE `manufacturer` = '" . $wid . "'";
                    $rows = $connection->fetchAll($query);
                    if (isset($rows[0])) {
                        $sentence = __($rows[0]["sentence"]);
                    } else if (isset($rows["sentence"])) {
                        $sentence= __($rows["sentence"]);
                    } else {
                       $sentence = "ships from our warehouse in 5 days";
                    }
                }
            } else {
                $sentence = "ready to ship";
            }

//	$sentence = "";

            $dataarr = [];
            $dataarr["sentence"] = $sentence;
            $dataarr["sku"] = $this->getSku();
            $dataarr["title"] = $this->getTitle();
            $dataarr["color"] = $this->getColor();
            $dataarr["size"] = $this->getSize();
            echo json_encode($dataarr);

            return;

        } else{
            $sentence = "error no pid specified";
            }

    }

    public function getLSInventory($pid) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $sql = "SELECT * FROM `amasty_multiinventory_warehouse_item` WHERE `product_id` = '".$pid."' AND `warehouse_id`=3";
        $rows = $connection->fetchAll($sql);
        if(isset($rows[0])) {
            return $rows[0]["qty"];
        } else {
            return false;
        }
    }

    public function getVendorInventory($pid) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $sql = "SELECT * FROM `amasty_multiinventory_warehouse_item` WHERE `product_id` = '".$pid."' AND `warehouse_id` != 3 AND `warehouse_id` != 1";
        $rows = $connection->fetchAll($sql);
        if(isset($rows[0])) {
            return $rows[0];
        } else {
            return false;
        }
    }

    public function getSku() {

        return $this->_productData->getSku();
    }

    public function getTitle() {
        return $this->_productData->getName();
    }

    public function getColor() {
        return $this->_productData->getAttributeText('color');
    }

    public function getSize() {
        return $this->_productData->getAttributeText('size');
    }
}
