<?php

namespace Sportpat\Sync\Helper;

class Update extends \Magento\Framework\App\Helper\AbstractHelper {


    private $_productModel;
    private $_product;
    private $_oldData;
    private $_newData;
    //Import to magento database helper

    protected $_dataHelper;
    protected $_productRepository;

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
    /** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory */
    protected $collectionFactory;

    protected $MATRIX_LOG_FILE ="/var/import/updateMatrix.log";


    //Configurable product vars
    protected $_usedConfigurableAttributeIds = [];
    protected $__configurableProductsData = [];

    protected $allmatrixdata;
    protected $_objectManager;

    protected $state;

    protected $mdata;

    public function __construct(
        Data $dataHelper,
        Images $imageHelper,
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
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\Product $productModel
    ) {

        $this->_dataHelper = $dataHelper;
        $this->_imageHelper = $imageHelper;
        $this->_objectManager = $objectManager;
        $this->state = $objectManager->get('Magento\Framework\App\State');
        $this->_productAttributeRepository = $productAttributeRepository;
        $this->_attributeOptionManagement = $attributeOptionManagement;
        $this->_optionLabelFactory = $optionLabelFactory;
        $this->_optionFactory = $optionFactory;
        $this->_productRepository = $productRepository;
        $this->_productFactory = $productFactory;
        $this->_linkManagement = $linkManagement;
        $this->_customerFactory = $customerFactory;
        $this->_entityAttributeCollection = $entityAttributeCollection;
        $this->_entityAttribute = $entityAttribute;
        $this->_attributeOptionCollection = $attributeOptionCollection;
        $this->_productModel = $productModel;
        $this->collectionFactory = $collectionFactory;

        $this->MATRIX_LOG_FILE =  $_SERVER["DOCUMENT_ROOT"].$this->MATRIX_LOG_FILE;

        if(!$this->state) {
            $this->setState();
        }

    }

    public function updateSimpleVisibility() {

        $coll = $this->collectionFactory->create();
        $coll->addAttributeToSelect('*')->addAttributeToFilter('type_id','simple');

        foreach($coll as $product) {
            $product->setVisibility(3);
            $product->save();
            echo $product->getName()." visibility updated"."\n";
        }

    }


    public function updateSimpleProductData($sku, $data, $storeID=0, $dataType="") {


        //fetch the product globally
            $this->getProductBySku($sku);

            //determine if the product is set
            if(is_object($this->_product)) {

                //the store id to update 0 = global, 1 = english, 2 = francais
                $this->_product->setStoreId($storeID);


                //verify the type of data to update then take the appropriate action
                switch($dataType) {


                    case 'Text':
                        $this->setTextData($data);
                        break;
                    case 'Attributes':
                        $this->setAttributeData($data);
                        break;
                    case 'Categories':
                        echo "NOT IMPLEMENTED";
                        break;
                    default:
                        echo "NOT IMPLEMENTED";
                        break;


                }


            }


    }

    public function updateGenders() {
        $coll = $this->collectionFactory->create();
        $coll->addAttributeToSelect('*')->addAttributeToFilter('gender',["in"=>[7370,7]]);
        $repo = $this->_objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface');
        $resource = $this->_objectManager->create('\Magento\Catalog\Model\ResourceModel\Product');
        foreach($coll as $product) {
            $p = $repo->get($product->getSku());


            $p->setStoreId(0);
            $p->setGender(6365);
            try {
                $resource->saveAttribute($p, 'gender');
                $p = $repo->get($p->getSku());

            } catch(\Exception $e) {
                die($e);
            }

            echo $p->getName()." saved with gender:".$p->getAttributeText("gender")."\n\n";
        }

    }

    public function setSimpleFrenchTranslations() {

        $filepath = "/home/sp227/fr_products.csv";
        $tData = file_get_contents($filepath);

        $lines = explode("\n", $tData);
        foreach($lines as $line) {



            $rows = explode(',',$line);
           // var_dump($rows); die();
            $productID = str_replace('""',"",$rows[0]);
            $trString = str_replace('""',"",$rows[1]);

            echo $productID;

            $this->_product = $this->_productModel->load($productID)->setStoreId(2);
            $this->_product->setName($trString);
            $this->_product->setUrlKey(null);
            $this->_product->save();

            echo $this->_product->getName(). "\n";

        }

    }

    public function setMatrixFrenchTranslations() {

        $filepath = "/home/sp227/fr_matrixes.csv";
        $tData = file_get_contents($filepath);

        $lines = explode("\n", $tData);
        foreach($lines as $line) {



            $rows = explode(',',$line);
            // var_dump($rows); die();
            $productID = str_replace('""',"",$rows[0]);
            $trString = str_replace('""',"",$rows[1]);

            echo $productID;

            $this->_product = $this->_productModel->load($productID)->setStoreId(2);
            $this->_product->setName($trString);
            $this->_product->setUrlKey(null);
            try {
                $this->_product->getResource()->saveAttribute($this->_product, 'name');
              //  $this->_product->getResource()->saveAttribute($this->_product, 'url_key');

            } catch(\Exception $e) {
                echo $e;
                echo $productID." has error";
                die();
                continue;
            }
            echo $this->_product->getName(). "\n";

        }

    }


    public function setTextData($data) {

        foreach($data as $d => $value) {

            $this->_product->setData($d, $value);

        }

        $this->_product->save();

    }

    public function setAttributeData($data) {

        foreach($data as $d => $value) {



        }

    }

    private function getProductBySku($sku) {


        /*
         * As magento2 is having a problem fetching by attributes products
         * with no visibility we need to add an extra step to find the
         * entity id and load the product. We use the inventory independent
         * database to do so.
         */


        $database = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'inventory',
            'server' => 'localhost',
            'username' => 'root',
            'password' => 'adv62062'
        ]);

        $query = "select * from products where sku = '".$sku."' LIMIT 1";
        $rows = $database->query($query)->fetchAll();


        if(!empty($rows)) {
            $p = $rows[0]["product_id"];

            $this->_product = $this->_productModel->load($p);

            return $p;}

        else return false;

    }

    public function setDescriptions($filename) {

        $file = "/home/sportpat/${filename}";
        $data = file_get_contents($file);
        $data = str_replace('"',"", $data);
        $expdata = explode("\n",$data);

        $i = 0;



                $repo = $this->_objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface');

                foreach($expdata as $d) {

            if($i > 0) {
                $cells = explode(";", $d);
                if(count($cells) > 1) {
                    $sku = $cells[0];

                    if(!isset($cells[2])) {
                    continue;
                    } else {
                        $desc = $cells[2];
                    }
                    if(!isset($cells[3])) {
                        var_dump($cells);
                        $descfr = $cells[2];
                    } else {
                        $descfr = $cells[3];
                    }
                    try {

                        $productF = $repo->get($sku);
                        $product = $productF;
                        $resource = $this->_objectManager->create('\Magento\Catalog\Model\ResourceModel\Product');
                        $product->setStoreId(1);
                        $product->setDescription($desc);
                        $resource->saveAttribute($product, 'description');
                        $productF->setStoreId(2);
                        $productF->setDescription($descfr);
                        $resource->saveAttribute($productF, 'description');
                        $product = $repo->get($productF->getSku());

                        echo $product->getName() . " " . $product->getDescription() . "\n\n";
                    } catch (\Exception $e) {
                        echo $e;
                        continue;
                    }

                } else {
                    continue;
                }
            }

            $i++;

        }


    }

    public function dedupeNames() {

        $coll = $this->collectionFactory->create();
        $coll->addAttributeToSelect('*')->addAttributeToFilter('type_id','configurable');
        $repo = $this->_objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface');
        $resource = $this->_objectManager->create('\Magento\Catalog\Model\ResourceModel\Product');

        foreach($coll as $product) {

            $name = $product->getName();
            $str = implode(',',array_unique(explode(',', $name)));
            $str = str_replace("   -", " -", $str);
            $str = str_replace(" wo ", " ", $str);
            $str = str_replace("jackets", "", $str);
            $str = str_replace("protections", "", $str);
            $str = str_replace("jerseys", "", $str);
            $str = str_replace("shirts", "", $str);
            $str = str_replace("helmets", "", $str);
            $str = str_replace("hoodies", "", $str);
            $str = str_replace("  ", " ", $str);
            $str = str_replace("t- ", " ", $str);

            $p = $repo->get($product->getSku());


            $p->setStoreId(0);
            $p->setName($str);
            try {
                $resource->saveAttribute($p, 'name');
                $p = $repo->get($p->getSku());

            } catch(\Exception $e) {
                die($e);
            }

            echo $p->getName()." saved"."\n\n";


        }


    }

    public function iterator() {
        $iterator = $this->_objectManager->create('\Magento\Framework\Model\ResourceModel\Iterator');
        return $iterator;
    }

    public function deletebadskus() {


        $iterator = $this->iterator();


        $coll = $this->_objectManager->create('\Magento\Catalog\Model\ResourceModel\Product\Collection');
        $coll->addAttributeToSelect('*')->addAttributeToFilter('type_id','simple')->addAttributeToFilter('sku', ['like' => '%-2020']);

        $iterator->walk(
            $coll->getSelect(),
            [[$this, 'delCallback']]);


    }

    public function delCallback($product) {
        $productF = $this->_productFactory->create();
        $check = $productF->load($product["row"]["entity_id"]);
        if (is_object($check)) {
            //  $this->_simplesproductids[] = $check->getId();
            // $this->replaceconfig = false;
            $check->delete();
        }
    }


}