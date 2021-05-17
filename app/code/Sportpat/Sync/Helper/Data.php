<?php
namespace Sportpat\Sync\Helper;
use MongoDB\Client;
use Google\Cloud\Translate\TranslateClient;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {


    //DATA related helpers (general)

    private $_client;
    private $_cursor_matrix;
    private $_cursor_item;

    const COLORSIZE_ATTRIBUTES = 4;
    const COLORONLY_ATTRIBUTES = 12;
    const SIZEONLY_ATTRIBUTES = 13;
    const TIRES_ATTRIBUTES = 10;
    const SIMPLE_ATTRIBUTES = 14;
    const OIL_ACCESSORIES_ATTRIBUTES = 15;

    private $projectId = "sunlit-hook-232817";


    public function __construct() {

        $this->_client = (new Client("mongodb://127.0.0.1:27017"))->sportpat_batchmay;

    }


    public function translate($text, $id, $target="fr") {
        $translate = new TranslateClient([
            'projectId' => $this->projectId
        ]);
        $translation = $translate->translate($text, [
            'target' => $target
        ]);

        $transFile = "/home/sportpat/fr_products.csv";
        $data = '"'.$id.'",'.'"'.$translation["text"].'"';

        $fh = fopen($transFile, 'a');
        fwrite($fh, "\n".$data);

        echo $data;

    }

    public function translateM($text, $id, $target="fr") {
        $translate = new TranslateClient([
            'projectId' => $this->projectId
        ]);
        $translation = $translate->translate($text, [
            'target' => $target
        ]);

        $transFile = "/home/sportpat/fr_matrixes.csv";
        $data = '"'.$id.'",'.'"'.$translation["text"].'"';

        $fh = fopen($transFile, 'a');
        fwrite($fh, "\n".$data);

        echo $data;

    }

    public function getMatrixDataForImport() {

        $mData = $this->_getMatrixes();


    }

    public function _getMatrixes($offset=0) {

        $m = $this->_client->matrixes;
        $options = ['sort' => ['_id' => 1]];

        $data = $m->find(['hasColor' => true], $options)->toArray();
        $prepared = [];
        $i=0;
       foreach($data as $d) {


          // $attributeSet = $this->checkAttributeSet($d);
//           $simples = $this->getSimplesIDForMatrix($d["sku"]);




               $prepared[$i] = [
                   "SKU" => $d["sku"],
                   "Name" => str_replace("WOMEN'S", "", $d["name"]),
                   "LSID" => $d["lightspeed_id"],
                   "AttributeSet" => 4,
                   "Description" => $d["description"],
                   "categories" => $d["categories"],
                   "Year" => $d["year"],
                   "Gender" => $d["gender"],
                   "Brand" => $d["brand"] ?? "",
                   "Images" => $d["images"]
               ];


               $i++;


       }
       return $prepared;


    }

    public function _getMatrixesTires($offset=0) {

        $m = $this->_client->matrixes;
        $options = ['sort' => ['_id' =>1], 'limit'=>100, 'skip'=>$offset];

        $data = $m->find(['itemType'=>'tires'], $options)->toArray();
        $prepared = [];
        $i=0;
        foreach($data as $d) {


            // $attributeSet = $this->checkAttributeSet($d);



            $prepared[$i] = [
                "SKU" => $d["sku"],
                "Name" => str_replace("WOMEN'S","" ,$d["name"]),
                "LSID" => $d["lightspeed_id"],
                "AttributeSet" => 4,
                "Description" => $d["description"],
                "categories" => $d["categories"],
                "Year" => $d["year"],
                "Gender" => $d["gender"],
                "Brand" => $d["brand"] ?? "",
                "Images" => $d["images"]
            ];


            $i++;

        }
        return $prepared;


    }


    public function _prepareMatrixes() {

        $attributeSet = 0;
        $prepared = [];

        $data = $this->_cursor_matrix;
        $i=0;

          /*  $attributeSet = $this->checkAttributeSet($d);

            $prepared[$i] = [
                "SKU" => $d["sku"],
                "Name" => $d["name"],
                "LSID" => $d["lightspeed_id"],
                "AttributeSet" => $attributeSet,
                "Description" => $d["description"],
                "Categories" => $d["categories"],
                "Year" => $d["year"],
                "Gender" => $d["gender"],
                "Brand" => $d["brand"],
                "Images" => $d["images"]
                ];

        }

        return $prepared;*/


    }

    public function getSimplesIDForMatrix($itemMatrixID) {

        $s = $this->_client->items;
        $simples = $s->find(['itemMatrixID' => $itemMatrixID]);
        $items = [];
        foreach($simples as $simple) {
            array_push($items, $simple["magentoSku"]);
        }
        return $items;

    }

    public function getAllSimplesTires() {
        $s = $this->_client->items;
        $simples = $s->find(['itemType'=>'tires'], ['skip'=>0,'sort'=>['_id'=>1]])->toArray();
        return $simples;
    }


    public function getAllSimplesColorSize() {
        $s = $this->_client->items;
        $simples = $s->find(['itemType'=>'colorsize'], ['skip'=>21500,'sort'=>['_id'=>1]])->toArray();
        return $simples;
    }

    public function getAllSimplesColorOnly() {
        $s = $this->_client->items;
        $simples = $s->find(['itemType'=>'colorsize','hasColor'=>true])->toArray();
        return $simples;
    }

    public function getAllSimplesSizeOnly() {
        $s = $this->_client->items;
        $simples = $s->find(['itemType'=>'colorsize','hasColor'=>null])->toArray();
        return $simples;
    }


    public function _getSimples($matrixID) {

        $s = $this->_client->items;
        $simples = $s->find(['itemMatrixID'=>$matrixID])->toArray();
        return $simples;

    }

    public function deleteProduct($data)
    {
        $products = $this->productFactory->create();

        $sku = $data["online_sku"];

        $pid = $products->getIdBySku($sku);

        if ($pid) {
            $products->load($pid)->delete();
            echo $pid . " deleted." . "\n";
        }
    }

    public function _prepareSimples() {



    }



    public function checkIfTire($data) {

        if(in_array("TIRES", $data["categories"])) {
            return true;
        }

        return false;

    }

    public function checkIfSimple($data) {

        if(count($this->_getSimples($data["itemMatrixID"])) <= 1) {
            return true;
        }
        return false;

    }

    public function checkIfOil($data) {

        if(in_array("OILS & CHEMICALS DEV", $data["categories"])) {
            return true;
        }

        return false;
    }

    public function checkAttributeSet($data) {


            $size= $data["hasSize"];
            $color = $data["hasColor"];
            $oil = $this->checkIfOil($data);
            $simple = $this->checkIfSimple($data);
            $tires = $this->checkIfTire($data);

            if($oil) { return self::OIL_ACCESSORIES_ATTRIBUTES;}
            elseif($tires) {return self::TIRES_ATTRIBUTES;}
            elseif($size === true && $color === false) {
                return self::SIZEONLY_ATTRIBUTES;
            }
            elseif($size === false && $color === true) {
                return self::COLORONLY_ATTRIBUTES;
            }
            elseif($size === true && $color === true) {
                return self::COLORSIZE_ATTRIBUTES;
            }
            elseif($simple) {
                return self::SIMPLE_ATTRIBUTES;
            }

            return false;

    }

    public function writeLog($data, $logFile="configurable_errors.log") {


            $fh = fopen("/home/sportpat/var/log/".$logFile, "a");
            fwrite($fh, "\n".$data);
            fclose($fh);



    }

}
