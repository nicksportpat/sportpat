<?php
namespace Sportpat\Product\Block\Rewrite\Product;

use \Magento\Catalog\Model\Product;


class ListProduct extends \Magento\Catalog\Block\Product\ListProduct {



    public function getBrands($name) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $conn = $resource->getConnection();
        $sql = 'SELECT * FROM amasty_amshopby_option_setting WHERE filter_code = "attr_brand" AND meta_title LIKE "%'.$name.'%"';
        $all = $conn->fetchAll($sql);
        return $all;
    }


    public function getBrandForProduct(\Magento\Catalog\Model\Product $product) {

        $brands = $this->getBrands($product->getAttributeText("brand"));
        return $brands;

    }

    public function getBrandImage(\Magento\Catalog\Model\Product $product) {

       $brandArray = $this->getBrandForProduct($product);


       if(is_array($brandArray) && isset($brandArray[0]["slider_image"])) {

            $bimage = "/media/amasty/shopby/option_images/slider/" . $brandArray[0]["slider_image"];
            if ($bimage == "/media/amasty/shopby/option_images/slider/") {
                $bimage = "/media/amasty/shopby/option_images/" . $brandArray[0]["image"];
            }
        } else{
            $bimage="";
        }

        return $bimage;

    }

    public function imagePreFetcher($product) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productfactory = $objectManager->create('\Magento\Catalog\Model\Product');
        $product = $productfactory->load($product->getId());

        $lsid = $product->getData("lightspeed_id");

        $optImage = "/home/sp227/pub/media/import/smalls/${lsid}.jpg";
        if(file_exists($optImage)) {
            $prefix = "data:image/jpeg;base64,";
            $baseimage = base64_encode(file_get_contents($optImage));
            return $prefix.$baseimage;
        } else {
            $optImage = "/home/sp227/pub/media/import/smalls/${lsid}.png";
            if(file_exists($optImage)) {
                $prefix = "data:image/png;base64,";
                $baseimage = base64_encode(file_get_contents($optImage));
                return $prefix.$baseimage;
            }
        }

        $image = $product->getImage();
        $dir = "/home/sp227/pub/media/catalog/product".$image;

        $filename = $dir;
        $prefix = "";
        $baseimage = "";
        if(strpos($filename, "jpg")) {
            $prefix = "data:image/jpeg;base64,";
        }
        if(strpos($filename, "png")) {
            $prefix = "data:image/png;base64,";
        }
        if(file_exists($filename)) {
            $baseimage = base64_encode(file_get_contents($filename));
        }
        return str_replace("/home/sp227/pub","",$optImage);

    }

    public function swatchPreFetcher($product) {


        $lsid = $product->getLightspeedId();
        $optImage = "/home/sp227/pub/media/import/swatches/${lsid}.jpg";
        if(file_exists($optImage)) {
            $prefix = "data:image/jpeg;base64,";
            $baseimage = base64_encode(file_get_contents($optImage));
            return $prefix.$baseimage;
        } else {
            $optImage = "/home/sp227/pub/media/import/swatches/${lsid}.png";
            if(file_exists($optImage)) {
                $prefix = "data:image/png;base64,";
                $baseimage = base64_encode(file_get_contents($optImage));
                return $prefix.$baseimage;
            }
        }

        $image = $product->getImage();
        $dir = "/home/sp227/pub/media/catalog/product".$image;

        $filename = $dir;
        $prefix = "";
        $baseimage = "";
        if(strpos($filename, "jpg")) {
            $prefix = "data:image/jpeg;base64,";
        }
        if(strpos($filename, "png")) {
            $prefix = "data:image/png;base64,";
        }
        if(file_exists($filename)) {
            $baseimage = base64_encode(file_get_contents($filename));
        }
        return str_replace("/home/sp227/pub","",$optImage);

    }

    public function getAllSwatches($product) {

        $children = $product->getTypeInstance()->getUsedProducts($product);
        $childlsids = [];
        $colors = [];

        $i=0;
        $currentColor = "";
        foreach($children as $child) {

            $colors[$i] = $child->getColor();
            $childlsids[$i] = $child->getId();

            $i++;
        }

        array_unique($colors);

        $dupids = [];
        $i=0;
        $n=0;
        foreach($children as $child) {

            for($n = 0; $n < count($colors); $n++) {

                if($child->getColor() == $colors[$n]){
                    $dupids[$colors[$n]] = $child->getId();
                }

            }

            $i++;

        }

        $html = "<div class='product-swatch-container-" . $product->getId() . "' style='   width:100%;
        height:auto;
        float:left;
        display:block;
        overflow:hidden;
        position:relative;'><div class='imageswatchcontainer' style='height:250px;' id='imgswcontainer_" . $product->getId() . "' onclick='window.location.href=\"" . $product->getProductUrl() . "\"'></div><div class='swcontainer'>";


        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        foreach($dupids as $prod => $id) {
            $mproduct = $objectManager->create('\Magento\Catalog\Model\Product')->load($id);
            $imurl = $this->swatchPreFetcher($mproduct);
            $lgurl = $this->imagePreFetcher($mproduct);
            $html .= "<div class='switem' style='display:block;float:left;text-align:center !important;'><img   src='" . $imurl . "' id='" . $id . "' style='padding:1px;' width='28px' height='28px' data-large='".$lgurl."' class='lazyload customswatches-" . $product->getId() . "' ></div>";


        }

        $html .= "</div></div>";
        return $html;



    }



    public function product_page_swatches($childproduct) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productfactory = $objectManager->create('\Magento\Catalog\Model\Product');
        $product = $productfactory->load($childproduct->getId());
        $lsid = $product->getLightspeedId();
        $optImage = "/home/sp227/pub/media/import/smalls/${lsid}.jpg";
        if(file_exists($optImage)) {
            $prefix = "data:image/jpeg;base64,";
            $baseimage = base64_encode(file_get_contents($optImage));
            return $prefix.$baseimage;
        } else {
            $optImage = "/home/sp227/pub/media/import/smalls/${lsid}.png";
            if(file_exists($optImage)) {
                $prefix = "data:image/png;base64,";
                $baseimage = base64_encode(file_get_contents($optImage));
                return $prefix.$baseimage;
            }
        }

        $image = $product->getImage();
        $dir = "/home/sp227/pub/media/catalog/product".$image;

        $filename = $dir;
        $prefix = "";
        $baseimage = "";
        if(strpos($filename, "jpg")) {
            $prefix = "data:image/jpeg;base64,";
        }
        if(strpos($filename, "png")) {
            $prefix = "data:image/png;base64,";
        }
        if(file_exists($filename)) {
            $baseimage = base64_encode(file_get_contents($filename));
        }
        return str_replace("/home/sp227/pub","",$optImage);

    }

}