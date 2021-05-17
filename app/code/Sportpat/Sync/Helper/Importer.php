<?php
namespace Sportpat\Sync\Helper;
error_reporting(E_ALL);
use MongoDB\Client;
class Importer extends \Magento\Framework\App\Helper\AbstractHelper
{

    private $_productModel;
    private $_mongo;
    private $_lastSku;
    private $_lastMongoID;
    private $_lastMatrixID;
    private $_currentSku;
    private $_dataItemsForMatrix;
    private $_currentCategory;
    private $_categoryFactory;
    private $_currentProductData;

    private $_errors;

    protected $_productAttributeModel;
    protected $_productAttributeRepository;
    protected $_attributeOptionManagement;
    protected $_attributeOptionCollection;
    protected $_optionLabelFactory;
    protected $_optionFactory;
    protected $_productFactory;
    protected $_linkManagement;
    protected $_entityAttributeCollection;
    protected $_entityAttribute;
    protected $_customerFactory;
    protected $_imageHelper;
    private $_objectManager;
    private $_productRepository;
    protected $state;
    private $categCollectionFactory;
    protected $_mongo2020;

    protected $_simplesproductids = [];

    protected $replaceconfig = false;

    public function __construct(
        Images $imageHelper,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\Product\Attribute\Repository $productAttributeRepository,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement,
        \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $optionLabelFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $linkManagement,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $entityAttributeCollection,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $attributeOptionCollection,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $cCollectionFactory

    )
    {

        $this->_imageHelper = $imageHelper;
        $this->_lastSku = "";
        $this->_lastMongoID = null;
        $this->_lastMatrixID = "";
        $this->_currentSku = null;
        $this->_dataItemsForMatrix = [];
        $this->_currentProductData = [];

        $this->_errors = "";


        $this->_productModel = $productModel;
        $this->_mongo = (new Client("mongodb://127.0.0.1:27017"))->sportpat_batchmay;
        $this->_mongo2020 = (new Client("mongodb://127.0.0.1:27017"))->sportpat_batchmay;
        $this->_categoryFactory = $categoryFactory;

        $this->_objectManager = $objectManager;
        $this->state = $objectManager->get('Magento\Framework\App\State');
        $this->_productAttributeRepository = $productAttributeRepository;
        $this->_attributeOptionManagement = $attributeOptionManagement;
        $this->_optionLabelFactory = $optionLabelFactory;
        $this->_optionFactory = $optionFactory;
        $this->_productRepository = $productRepository;
        $this->_productFactory = $productFactory;
        $this->_linkManagement = $linkManagement;

        $this->_entityAttributeCollection = $entityAttributeCollection;
        $this->_entityAttribute = $entityAttribute;
        $this->_attributeOptionCollection = $attributeOptionCollection;
        $this->categCollectionFactory = $cCollectionFactory;


    }

    public function logErrorToFile()
    {

    }

    public function writeJSONToFile($data, $filename)
    {
        $path = "/data/home/sportpat/var/import/data/" . str_replace(" ", "_", $this->_currentCategory) . "-" . $filename . ".json";
        $content = json_encode($data, JSON_UNESCAPED_SLASHES);
        file_put_contents($path, $content);

        echo $path . "\n";

    }




    public function getConfigurableProduct($year=null)
    {

echo "getting conf";

    $m = $this->_mongo->matrixes;
    $options = ['sort' => ['_id' => 1]];
    $data = $m->find([], $options)->toArray();
    var_dump($data);

        $i = 0;
        foreach ($data as $matrix) {


            var_dump($matrix);

            $matrixarray = $this->buildConfigurableProductArray($matrix);

            if($year !== null) {
                $this->getSimpleProductsForMatrix2020($matrix["lightspeed_id"]);
            } else {
                $this->getSimpleProductsForMatrix($matrix["lightspeed_id"]);
            }

            if (!empty($this->_dataItemsForMatrix)) {
                $matrixarray["simples"] = $this->_dataItemsForMatrix;
            }

            $this->writeJSONToFile($matrixarray, $matrixarray["sku"]);

        }


    }

    public function getSimpleProductsForMatrix2020($matrixID)
    {
        $this->_dataItemsForMatrix = [];
        $p = $this->_mongo2020->items;
        $data = $p->find(['itemMatrixID' => $matrixID])->toArray();
        foreach ($data as $item) {
            $this->_dataItemsForMatrix[] = $this->buildSimpleProductArray($item, $item["itemType"]);
        }

    }


    public function getSimpleProductsForMatrix($matrixID)
    {
        $this->_dataItemsForMatrix = [];
        $p = $this->_mongo->items;
        $data = $p->find(['itemMatrixID' => $matrixID])->toArray();
        foreach ($data as $item) {
            $this->_dataItemsForMatrix[] = $this->buildSimpleProductArray($item, $item["itemType"]);
        }

    }

    public function getRawAttributeArray($item)
    {
        $attributes = $item['attributes'] ?? null;

        if ($attributes === null) {
            return null;
        }
        $att = [];
        foreach ($attributes as $attr) {
            if ($attr['name'] == 'Color') {
                $att['Color'] = $attr['value'];
            } else if ($attr['name'] == 'Size') {
                $att['Size'] = $attr['value'];
            }
        }

        return $att;
    }

    public function buildSimpleProductArray($item, $type)
    {

        $itemArray = [];
        $attArray = $this->getRawAttributeArray($item);

        $itemArray["sku"] = $item["magentoSku"];
        $itemArray["name"] = $item["name"];
        $itemArray["store_id"] = 0;
        $itemArray["website_ids"] = [1];

        if (isset($item["categories"][2]) && false !== strpos($item["categories"][2], "TIRE")) {
            $itemArray["attribute_set_id"] = 9;
        } else {
            $itemArray["attribute_set_id"] = 4;
        }
        $itemArray["lightspeed_id"] = $item["lightspeed_id"];
        $itemArray["visibility"] = 1;
        $itemArray["status"] = 1;
        $itemArray["type_id"] = "simple";
        $itemArray["price"] = $item["price"];
        $itemArray["qty"] = 0;
        $itemArray["brand"] = $item["brand"] ?? "BLIZZARD";
        if ($type !== "tire") {
            $itemArray["size"] = $attArray["Size"] ?? null;
            $itemArray["color"] = $attArray["Color"] ?? null;
            $itemArray["gender"] = $item["gender"] ?? null;

        } else {
            $itemArray["size"] = $attArray["Size"] ?? null;
        }

        $itemArray["year"] = $item["year"] ?? "0000";
        $itemArray["description"] = "";
        $itemArray["short_description"] = "";
        $itemArray["images"] = $item["images"] ?? null;
        $itemArray["categories"] = $item["categories"] ?? null;

        return $itemArray;


    }

    public function buildConfigurableProductArray($matrix)
    {



        $confArray = [];
        $confArray["sku"] = $matrix["sku"];
        $confArray["name"] = $matrix["name"];
        $confArray["store_id"] = 0;
        $confArray["website_ids"] = [1];
        if (isset($matrix["categories"][2]) && false !== strpos($matrix["categories"][2], "TIRE")) {
            $confArray["attribute_set_id"] = 9;
        } else {
            $confArray["attribute_set_id"] = 4;
        }
        $confArray["lightspeed_id"] = $matrix["lightspeed_id"];
        $confArray["visibility"] = 4;
        $confArray["status"] = 1;
        $confArray["type_id"] = "configurable";

        $confArray["brand"] = $matrix["brand"] ?? "BLIZZARD";
        $confArray["gender"] = $matrix["gender"] ?? null;
        $confArray["year"] = "2021";
        $confArray["description"] = "";
        $confArray["short_description"] = "";
        $confArray["simples"] = [];
        $confArray["images"] = $matrix["images"] ?? null;
        $confArray["categories"] = $matrix["categories"] ?? null;

        $this->_currentCategory = $matrix["categories"][0];

        echo $confArray["brand"]."\n\n";

            return $confArray;


    }

    public function loadOneFromJSON()
    {

        $errorText = "";
        $fd = scandir("/data/home/sportpat/var/import/data/");
        foreach ($fd as $file) {
            if ($file !== "." && $file !== "..") {

                try {
                    $id = str_replace(".json", "", explode("-", $file)[3]);
                    // if($id != "17274") {

                    echo $file;
                    $content = file_get_contents("/data/home/sportpat/var/import/data/" . $file);
                    $data = json_decode($content, true);

                    //var_dump($data["simples"]);

                    $this->createSimples($data["simples"]);
                    $this->createConfigurable($data);

                   //  }
                } catch(\Exception $e) {
                   $errorText = $data;
                    die($errorText);
                    continue;
                }

                
            }
        }
        if($errorText != "") {
            file_put_contents("/data/home/sportpat/var/importerrors.log", $data);
        }

        return null;

    }

    public function initProductImport()
    {


 $this->getConfigurableProduct();

  $this->loadOneFromJSON();
//$this->updateYears();

    }

    public function initProductImport2020()
    {


    $this->getConfigurableProduct("2021");

         $this->loadOneFromJSON();


    }

    public function updateYears() {
        $fd = scandir("/home/sportpat/var/import/data/");
        foreach ($fd as $file) {
            if ($file !== "." && $file !== "..") {


                $id = explode("-", $file)[3];

                echo $file;
                $content = file_get_contents("/home/sportpat/var/import/data/" . $file);
                $data = json_decode($content, true);

                $simples = $data["simples"];
                foreach($simples as $d) {
                    $product = $this->_productFactory->create();
                    $p = $product->loadByAttribute('lightspeed_id', $d['lightspeed_id']);
                    if(is_object($p)) {
                        $name = $p->getName();
                        $brand = $p->getAttributeText("brand");
                        $p->setName(str_replace("2021", $brand." ", $name));

                        $p->setYear($this->getAttributeValueByName('2021', 'year'));
                        $p->save();
                        echo $p->getName() ." year:".$p->getYear() . "\n";
                    }
                    else {
                        continue;
                    }

                }



            }
        }
    }


    public function createSimples($datas)
    {


        $this->_simplesproductids = [];

        foreach ($datas as $data) {

            $images = "";
            $productF = $this->_productFactory->create();
            $check = $productF->loadByAttribute('sku', $data["sku"]);
            if (is_object($check)) {
            // $this->_simplesproductids[] = $check->getId();
             // $this->replaceconfig = false;
                  $check->delete();
            }

         /*   $check = $productF->loadByAttribute('sku', $data["sku"]);
            if (is_object($check)) {
                $check->delete();
            }*/

            $this->replaceconfig = true;

            $gender = null;
            $product = $this->_objectManager->create('\Magento\Catalog\Model\Product');
            $product->setSku($data['sku']);

            $product->setStoreId($data["store_id"]);
            $product->setWebsiteIds([1]);
            $product->setAttributeSetId($data["attribute_set_id"]); // Attribute set id
            $product->setLightspeedId($data['lightspeed_id']);
            $product->setVisibility(1);
            $product->setStatus(1);
            $product->setWeight(0);

            $categories = $data["categories"];
            $catsize = count($categories) - 1;

            $product->setCategoryIds($this->setCategories($data));

            $catName = strtolower(str_replace("DEV", "", $data["categories"][$catsize]));

            $name = $this->removeDupeFromName(strtolower($data["name"]));

            $product->setName($name);

            $product->setTypeId('simple');
            $product->setPrice($data['price']);
            $product->setLightspeedId($data['lightspeed_id']);

            $product->setStockData(
                array(
                    'use_config_manage_stock' => 1,
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => 0
                )
            );

            if ($data["brand"]) {
                $product->setBrand($this->getAttributeValueByName(strtoupper($data['brand']), 'brand'));
            }

            if (false !== strpos($data['name'], "adult")) {
                $gender = "ADULT";
            } else {
                $gender = $data["gender"] ?? null;
            }
            if ($gender) {
                $product->setGender($this->getAttributeValueByName($gender, 'gender'));
            }
            $product->setYear($this->getAttributeValueByName('2021', 'year'));

            if ($data["color"] != "" && $data["attribute_set_id"] !== 9) {
                $product->setColor($this->getAttributeValueByName($data['color'], 'color'));
            }

            if ($data["size"] != "" && $data["attribute_set_id"] !== 9) {
                $product->setSize($this->getAttributeValueByName($data['size'], 'size'));
            } else if ($data["size"] != "" && $data["attribute_set_id"] === 9) {
                $product->setTireSize($this->getAttributeValueByName($data['size'], 'tire_size'));
            }

            $product->setDescription(strtolower($data['name']));
            $product->setShortDescription('');

            $seolink = $this->generateSeoURL($this->keygen() . "-" . $data["name"]);

            $product->setUrlKey($this->keygen() . "-" . $seolink);

            $images = $this->_imageHelper->getNewImages($data['lightspeed_id']);

            if ($images === null) {
                $this->_errors = $this->_errors . "\n" . "(SKIPPED) images not found for sku: " . $data["sku"];
                file_put_contents("/home/sportpat/var/log/product-import-errors.log", $this->_errors);
               die($this->_errors); continue;
            } //else {

                if (is_array($images) && !empty($images)) {
                    foreach ($images as $imagea) {

                        $image = $imagea['image'];
                        $position = $imagea['position'];

                        echo $image . "\n\n";


                        if (file_exists($image) && $position !== null) {

                            if ($position == "0") {

                                $product->addImageToMediaGallery($image, array('image', 'base', 'thumbnail', 'small_image', 'swatch'), true, false);
                            } else {

                                $product->addImageToMediaGallery($image, null, false, false);
                            }


                        }
                    }
                }
                    try {
                        $product->save();
                    } catch (\Exception $e) {
                     die($e);
                        $this->_errors = $this->_errors . "\n" . "(SKIPPED) error for sku: " . $data["sku"]. "error:".$e;
                        file_put_contents("/home/sportpat/var/log/product-import-errors.log", $this->_errors);
                        continue;
                    }
                    $this->_simplesproductids[] = $product->getId();

                    echo $product->getName() . "\n";
                }





        return;

    }

    function keygen()
    {
        $chars = "abcdefghijklmnopqrstuvwxyz";
        $chars .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $chars .= "0123456789";
        while (1) {
            $key = '';
            srand((double)microtime() * 1000000);
            for ($i = 0; $i < 4; $i++) {
                $key .= substr($chars, (rand() % (strlen($chars))), 1);
            }
            break;
        }
        return $key;
    }

    function generateSeoURL($string, $wordLimit = 0)
    {
        $separator = '-';

        if ($wordLimit != 0) {
            $wordArr = explode(' ', $string);
            $string = implode(' ', array_slice($wordArr, 0, $wordLimit));
        }

        $quoteSeparator = preg_quote($separator, '#');

        $trans = array(
            '&.+?;' => '',
            '[^\w\d _-]' => '',
            '\s+' => $separator,
            '(' . $quoteSeparator . ')+' => $separator
        );

        $string = strip_tags($string);
        foreach ($trans as $key => $val) {
            $string = preg_replace('#' . $key . '#i' . ('UTF8_ENABLED' ? 'u' : ''), $val, $string);
        }

        $string = strtolower($string);

        return trim(trim($string, $separator));
    }

    function removeDupeFromName($name) {
       return preg_replace("/\b(\w+)\s+\\1\b/i", "$1", $name);
    }

    public function createConfigurable($data)
    {
        $productF = $this->_productFactory->create();
        $check = $productF->loadByAttribute('sku', $data["sku"]);



        if(!empty($this->_simplesproductids)) {

            if (is_object($check)) {
            //    if($this->replaceconfig === true) {
                      $check->delete();
             //   } else {
            //        return;
            //    }
            }

        } else {
            return;
        }



        $product = $this->_objectManager->create('\Magento\Catalog\Model\Product');
        $product->setSku($data['sku']);

        $product->setStoreId($data["store_id"]);
        $product->setWebsiteIds([1]);
        $product->setAttributeSetId($data["attribute_set_id"]); // Attribute set id
        $product->setLightspeedId($data['lightspeed_id']);
        $product->setVisibility(4);
        $product->setStatus(1);
        $product->setWeight(0);
        $product->setPrice(0);

        $categories = $data["categories"];
        $catsize = count($categories) - 1;

        $product->setCategoryIds($this->setCategories($data));

        $catName = strtolower(str_replace("DEV", "", $data["categories"][0]));


        $name = $this->removeDupeFromName($data["name"]);

        echo $name."\n\n";

        $product->setName($name);

        $product->setTypeId('configurable');

        $seolink = $this->generateSeoURL($this->keygen() . "-" . $name);

        $product->setUrlKey($seolink);


        if ($data["brand"]) {
            $product->setBrand($this->getAttributeValueByName(strtoupper($data['brand']), 'brand'));
        }

        if (false !== strpos($data['name'], "adult")) {
            $gender = "ADULT";
        } else {
            $gender = $data["gender"] ?? null;
        }
        if ($gender) {
            $product->setGender($this->getAttributeValueByName($gender, 'gender'));
        }
        $product->setYear($this->getAttributeValueByName($data['year'], 'year'));


        $product->setDescription(strtolower($data['name']));
        $product->setShortDescription('');


        $ids = $this->_simplesproductids;

        $i = 0;
        $_configurableProductsData = [];
        $usedattributeids = [];
        foreach ($ids as $id) {

            $_configurableProductsData[$id] = [];
            $products = $this->_productFactory->create();
            $simple = $products->load($id);
            $color = $simple->getColor();
            $size = $simple->getSize() ?? $simple->getTireSize();

            if ($color) {
                $_configurableProductsData[$id][0] = [
                    'label' => '',
                    'attribute_id' => '93', //color
                    'value_index' => $color,
                    'is_percent' => '0',
                    'pricing_value' => '0',
                ];
                $usedattributeids[] = 93;
                if ($size) {
                    $_configurableProductsData[$id][1] = [
                        'label' => '',
                        'attribute_id' => '144', //color
                        'value_index' => $size,
                        'is_percent' => '0',
                        'pricing_value' => '0',
                    ];
                    $usedattributeids[] = 144;
                }
            } else {
                if ($size && $data["attribute_set_id"] == 4) {
                    $_configurableProductsData[$id][0] = [
                        'label' => '',
                        'attribute_id' => '144', //color
                        'value_index' => $size,
                        'is_percent' => '0',
                        'pricing_value' => '0',
                    ];
                    $usedattributeids[] = 144;
                } else if ($size && $data["attribute_set_id"] == 9) {
                    $_configurableProductsData[$id][0] = [
                        'label' => '',
                        'attribute_id' => '209', //color
                        'value_index' => $size,
                        'is_percent' => '0',
                        'pricing_value' => '0',
                    ];
                    $usedattributeids[] = 209;
                }
            }

        }

        $product->getTypeInstance()
            ->setUsedProductAttributeIds(array_filter($usedattributeids), $product);

        $configurableAttributesData = $product->getTypeInstance()
            ->getConfigurableAttributesAsArray($product);

        $associatedProductIds = array_keys($_configurableProductsData);
        $product->setCanSaveConfigurableAttributes(true);
        $product->setAssociatedProductIds($associatedProductIds);
        $product->setConfigurableAttributesData($configurableAttributesData);
        $product->setConfigurableProductsData($_configurableProductsData);

        $extensionConfigurableAttributes = $product->getExtensionAttributes();
        $extensionConfigurableAttributes->setConfigurableProductLinks($associatedProductIds);

        foreach ($configurableAttributesData as $key => $d) {

            $attributeValues = array();
            foreach ($d['options'] as $opt) {
                $attributeValues[] = array(
                    'label' => $opt['label'],
                    'attribute_id' => $key,
                    'value_index' => $opt['value']
                );
            }

            $extensionConfigurableOptions[] = array(
                'attribute_id' => $key,
                'code' => $d['attribute_code'],
                'label' => $d['frontend_label'],
                'position' => '0',
                'values' => $attributeValues,
            );
        }

        $images = $this->_imageHelper->getNewImagesForMatrix($data['lightspeed_id']);

        if ($images === null) {
            $this->_errors = $this->_errors . "\n" . "(SKIPPED) images not found for sku: " . $data["sku"];
            file_put_contents("/home/sportpat/var/log/product-import-errors.log", $this->_errors);
            return;
        } else {

            if (is_array($images) && !empty($images)) {
                foreach ($images as $imagea) {

                    $image = $imagea['image'];
                    $position = $imagea['position'];

                    echo $image;

                    if (file_exists($image) && $position !== null) {

                        if ($position == "0") {

                            $product->addImageToMediaGallery($image, array('image', 'base', 'thumbnail', 'small_image', 'swatch'), false, false);
                        } else {

                            $product->addImageToMediaGallery($image, null, false, false);
                        }

                    }
                }
                try {
                    $product->setStockData(
                        array(
                            'use_config_manage_stock' => 1,
                            'manage_stock' => 1,
                            'is_in_stock' => 1,
                            'qty' => 0
                        )
                    );
                    $product->save();


                } catch (\Exception $e) {
                    $this->_errors = $this->_errors . "\n" . "(SKIPPED) error on matrix: " . $data["lightspeed_id"].":".$e;
                    file_put_contents("/home/sportpat/var/log/product-import-errors.log", $this->_errors);
                    return;
                }
            }
        }

        echo $product->getName() . "-" . $product->getSku() . "\n";

        return;


    }


    public function getCategoryByName($catName, $parentname)
    {


        $catids = [];
        $collection = $this->_categoryFactory->create()->getCollection()->addAttributeToFilter('name', str_replace('DEV', "", $parentname))->addAttributeToSelect('parent_category');
        foreach ($collection as $category) {

            $childs = $category->getChildrenCategories();

            foreach ($childs as $child) {
                $catfactory = $this->_objectManager->get('Magento\Catalog\Model\CategoryFactory');
                $children = $catfactory->create()->load($child->getId());

                if (trim($children->getName()) == trim($catName)) {
                    return [2, $category->getId(), $children->getId()];
                } else {

                    $childs = $category->getChildrenCategories();

                    foreach ($childs as $child) {
                        $catfactory = $this->_objectManager->get('Magento\Catalog\Model\CategoryFactory');
                        $children = $catfactory->create()->load($child->getId());
                        echo $children->getName();

                        if (trim($children->getName()) == trim($catName)) {
                            return [2, $category->getId(), $children->getId()];
                        } else {

                            $childs = $category->getChildrenCategories();

                            foreach ($childs as $child) {
                                $catfactory = $this->_objectManager->get('Magento\Catalog\Model\CategoryFactory');
                                $children = $catfactory->create()->load($child->getId());
                                echo $children->getName();


                                if (trim($children->getName()) == trim($catName)) {
                                    return [2, $category->getId(), $children->getId()];
                                }
                            }

                        }

                    }

                }

            }


        }


    }


    public function setCategories($data)
    {

        // $this->state->setAreaCode('frontend');

        $i = 0;
        $id = 2;
        $sid = 0;


        $categs = $data["categories"];

        $sub = $categs;


        $collection = $this->categCollectionFactory->create()
            ->addAttributeToFilter('name', str_replace("DEV", "", strtoupper($sub[0])))
            ->setPageSize(1);

        if ($collection->count() > 0) {
            $id = $collection->getFirstItem()->getId();
        } else {


            if (false !== strpos(strtolower($sub[0]), "oil")) {
                $id = 449;


            } else {

                switch (strtolower(trim($sub[0]))) {

                    case "atv utv":
                        $id = 757;
                        break;
                    case "casual dev":
                        $id = 27;
                        break;
                    case "casual":
                        $id = 27;
                        break;
                    case "dirt bike dev":
                        $id = 24;
                        break;
                    case "dirt bike":
                        $id = 24;
                        break;
                    case "motorcycle dev":
                        $id = 28;
                        break;
                    case "motorcycle":
                        $id = 28;
                        break;
                    case "snowmobile dev":
                        $id = 640;
                        break;
                    case "snowmobile":
                        $id = 640;
                        break;
                    case "bags dev":
                        $id = 426;
                        break;
                    case "bags":
                        $id = 426;
                        break;
                    case "electrical dev":
                        $id = 433;
                        break;
                    case "electrical":
                        $id = 433;
                        break;

                    case "garage accessories":
                        $id = 443;
                        break;

                    case "tools":
                        $id = 465;
                        break;
                    case "tools dev":
                        $id = 465;
                        break;
                    case "communication systems":
                        $id = 480;
                        break;
                    case "communication systems dev":
                        $id = 480;
                        break;
                    case "engine":
                        $id = 483;
                        break;
                    case "engine dev":
                        $id = 483;
                        break;
                    case "transmission & drive":
                        $id = 485;
                        break;
                    case "transmission & drive dev":
                        $id = 485;
                        break;
                    case "oils & chemicals":
                    case "oils & chemicals dev":
                        $id = 449;
                        break;
                    case "tires accessories":
                    case "tires accessories dev":
                        $id = 375;
                        break;
                    case "fan gear":
                    case "fan gears":
                    case "fan gears dev":
                    case "fan gear dev":
                        $id = 368;
                        break;
                    case "oil filters":
                    case "oil filters dev":
                        $id = 732;
                        break;
                    case "heated gear":
                        $id=493;
                        break;
                    case "stickers":
                        $id=730;
                        break;
                    case "chain":
                        $id=733;
                        break;
                    case "air filters":
                    case "air filters dev":
                        $id = 734;
                        break;

                    case "":
                        $id = 2;
                        break;
                }
            }
        }

        if ($id != null) {

            $carray = [];
            $catfactory = $this->_objectManager->get('Magento\Catalog\Model\CategoryFactory');
            $category = $catfactory->create()->load($id);
            $childs = $category->getChildrenCategories();
            $ssid = 0;
            $sssid = 0;
            $ssssid = 0;

            $categs = "";

            //    echo $id;


            if (isset($sub[1])) {
                foreach ($childs as $child) {

                    //  echo $child->getName()." : ".$sub[1]."\n";

                    if ($id == 24 && false !== strpos($sub[1], "PARTS")) {
                        //     var_dump($sub);
                        $sid = 363;
                    }
                    if ($id == 28 && false !== strpos($sub[1], "PARTS")) {
                        //   var_dump($sub);
                        $sid = 498;
                    }

                    if ($id == 640 && false !== strpos($sub[1], "PARTS")) {
                        //   var_dump($sub);
                        $sid = 642;
                    }

                    if ($id == 757 && false !== strpos($sub[1], "PARTS")) {
                        //   var_dump($sub);
                        $sid = 759;
                    }


                    $cname = htmlentities(trim($child->getName()));

                    if ($sid == 0 && trim(strtolower($cname)) == strtolower(trim($sub[1]))) {
                        $sid = $child->getId();
                        $categs .= $sub[1] . "\n\n";

                        if (isset($sub[2])) {
                            $categs .= $sub[2] . "\n\n";

                            $ssid = $this->getSubCategory($sid, $sub[2]);
                            if (isset($sub[3])) {
                                $sssid = $this->getSubCategory($ssid, $sub[3]);
                                $categs .= $sub[3] . "\n\n";

                                if (isset($sub[4])) {
                                    $ssssid = $this->getSubCategory($sssid, $sub[4]);
                                    $categs .= $sub[4] . "\n\n";
                                }

                            }

                        }

                    } elseif ($sid != 0) {
                        if (isset($sub[2])) {
                            $categs .= $sub[2] . "\n\n";

                            $ssid = $this->getSubCategory($sid, $sub[2]);
                            if (isset($sub[3])) {
                                $sssid = $this->getSubCategory($ssid, $sub[3]);
                                $categs .= $sub[3] . "\n\n";

                                if (isset($sub[4])) {
                                    $ssssid = $this->getSubCategory($sssid, $sub[4]);
                                    $categs .= $sub[4] . "\n\n";
                                }

                            }

                        }
                    }


                }
                //   echo $categs;
                if ($sid != 0) {
                    $carray = [$id, $sid];
                }

                if ($ssid != 0) {
                    $carray = [$id, $sid, $ssid];
                }
                if ($sssid != 0) {
                    $carray = [$id, $sid, $ssid, $sssid];
                }
                if ($ssssid != 0) {
                    $carray = [$id, $sid, $ssid, $sssid, $ssssid];
                }


            }

            /* if($sid != 0) {
                 $carray = [$id, $sid];
             }*/


            if ($sid == 0) {
                $carray = [$id];
            }         // var_dump($carray); die();


            return $carray;
        }



    }






    public function getSubCategory($id, $subname) {
        $catfactory = $this->_objectManager->get('Magento\Catalog\Model\CategoryFactory');
        $category = $catfactory->create()->load($id);
        $childs = $category->getChildrenCategories();
        foreach($childs as $child){
            if(strtoupper(trim($subname)) == strtoupper(trim($child->getName()))){
                return $child->getId();
            }
        }
    }






    public function getAttributeValueByName($name, $code)
    {

        $value = false;

        $name = str_replace("/", " ", $name);

      //  echo $name;
     //   echo $code;


        $attribute = $this->_productAttributeRepository->get($code)->getOptions();
        foreach ($attribute as $option) {


            if ($option->getLabel() == $name) {
                $value = $option->getValue();
                return $value;
            }
        }

        if ($value == false) {

            if (strlen($name) > 1) {

             $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $attributeRepository = $objectManager->create('\Magento\Eav\Model\AttributeRepository');
                $attributeId = $attributeRepository->get('catalog_product', $code)->getAttributeId();
                $option = $objectManager->create('\Magento\Eav\Model\Entity\Attribute\Option');
                $attributeOptionLabel = $objectManager->create('\Magento\Eav\Api\Data\AttributeOptionLabelInterface');
                $attributeOptionManagement = $objectManager->create('\Magento\Eav\Api\AttributeOptionManagementInterface');

                $attributeOptionLabel->setStoreId(0);
                $attributeOptionLabel->setLabel($name);
                $option->setLabel($name);
                $option->setValue($name);
                $option->setStoreLabels([$attributeOptionLabel]);
                $option->setSortOrder(0);
                $option->setIsDefault(false);

                $attributeOptionManagement->add('catalog_product', $attributeId, $option);
                return $optionId = $this->getOptionId($code,$name);

            }


        }

    }

    function getOptionId($atributeCode,$optionValue){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $eaov = $resource->getTableName('eav_attribute_option_value');
        $eao = $resource->getTableName('eav_attribute_option');
        $ea= $resource->getTableName('eav_attribute');
        $attributeId = $connection->fetchOne("SELECT `attribute_id` FROM $ea WHERE `attribute_code` = '$atributeCode' AND `entity_type_id` = '4'");
        $sql = "select * from $eao join $eaov on $eaov.option_id = $eao.option_id where $eaov.value='$optionValue' AND $eao.attribute_id='$attributeId'";
        $result = $connection->fetchRow($sql);
        return $optionId = isset($result['option_id']) ? $result['option_id']: null;
    }

    public function getAttributeLabel($id, $code)
    {

        $attribute = $this->_productAttributeRepository->get($code)->getOptions();
        foreach ($attribute as $option) {

            if ($option->getValue() == $id) {
                return $option->getLabel();
            }

        }
        return 0;


    }

    public function getAttributeValues($code)
    {

        $attribute = $this->_productAttributeRepository->get($code)->getOptions();
        $r = [];
        foreach ($attribute as $a) {
            $r[] = array($a->getValue() => $a->getLabel());
        }

        return $r;
    }


    public function seo_friendly_url($string)
    {
        $string = str_replace(array('[\', \']'), '', $string);
        $string = preg_replace('/\[.*\]/U', '', $string);
        $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string);
        $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/'), '-', $string);
        return strtolower(trim($string, '-'));
    }

    public function attributeByCode($code)
    {
        $attribute = $this->_entityAttribute
            ->loadByCode('catalog_product', $code);
        $data = $attribute->getData('attribute_id');
        return $data;

    }

}
