<?php

namespace Nick\Algoliasearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ResourceConnection;

class BrandIndexObserver implements ObserverInterface
{

    protected $_connection;
    protected $_sizeerror = "";

    public function __construct(\Magento\Catalog\Model\ProductFactory $productFactory, ResourceConnection $connection)
    {
        // Observer initialization code...
        // You can use dependency injection to get any class this observer may need.
        $this->_connection = $connection;

    }

    public function execute(Observer $observer)
    {
        // Observer execution code...
        // Here you can modify frontend configuration

        // Example:
        // productsSettings = $observer->getData('index_settings');
        // $productsSettings['foo'] = 'bar';
        $this->_sizeerror = "";

        $product = $observer->getData('productObject');
        $productRecord = $observer->getData('custom_data');

        $brand = $product->getBrand();
        $brandinfoEn = $this->getBrandInfo($brand, 0);
        $brandinfoFr = $this->getBrandInfo($brand, 2);
        $index = $this->buildBrandIndex($brandinfoEn, $brandinfoFr, $brand);


        $gender = $productRecord['gender'];
        if($gender == "MEN" || $gender == "HOMME") {
            $genderIcon = "1family-men.png";
        } elseif($gender == "WOMEN'S" || $gender == "WOMEN" || $gender == "FEMMES" || $gender == "FEMME") {
            $genderIcon = "1family-women-grey.png";
        } elseif($gender == "YOUTH" || $gender == "JEUNE" || $gender == "ENFANT") {
            $genderIcon = "1family-child.png";
        } else {
            $genderIcon = "1family-child-gris.png";
        }

        $productRecord["brand_info"] = $index;
        $productRecord["gender_icon"] = $genderIcon;

        if($this->_sizeerror !== "") {
            $filename = "/data/home/sportpat/var/log/branderror_".strtolower(str_replace(" ", "_",$product->getAttributeText("brand")))."_sizeerror.log";
            file_put_contents($filename, $this->_sizeerror);
        }



    }

    public function getBrandInfo($brandname, $storeid) {

        $query = "SELECT * FROM amasty_amshopby_option_setting WHERE value = '".$brandname."' AND store_id = '".$storeid."'";
        $r = $this->_connection->getConnection()->query($query);
        $res = $r->fetchAll();
        if(isset($res[0])) {
            return $res[0];
        }
        return $res;

    }

    public function buildBrandIndex($info1, $info2, $brand) {


        $description = "";
        if(isset($info1["description"])) {
            $description = $info1["description"] ?? "";
        }

            $isFeatured = $info1["is_featured"] ?? false;
            $title = $info1["title"] ?? "";

            $image = $info1["image"] ?? "";
            $sliderImage = $info1["slider_image"] ?? "";


            $description2 = "";
        if(isset($info2["description"])) {
            $description2 = $info2["description"] ?? "";
        }



        $index = [
            "description" => [],
            "brand_value" => $brandValue ?? null,
            "featured" => $isFeatured ?? 0,
            "title" => $title ?? "",
            "image" => $image ?? "",
            "slider_image" => $sliderImage ?? "",
            "url_alias" => $info1["url_alias"] ?? ""

        ];

        $index["description"] = [
            "en" => $description ?? "",
            "fr" => $description2 ?? ""
        ];



        if(mb_strlen($index["description"]["en"]) >= 2000) {
            
            $this->_sizeerror .= "error at brand id: ${brand} description was too long. Size: ".mb_strlen($index["description"]["en"])."bytes"."\n\n";
            $index["description"]["en"] = "";
            $index["description"]["fr"] = "";
        }

        return $index;

    }

}

