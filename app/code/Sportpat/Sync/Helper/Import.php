<?php
namespace Sportpat\Sync\Helper;
error_reporting(E_ALL);
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Setup\CategorySetup;
use Magento\ConfigurableProduct\Helper\Product\Options\Factory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\TestFramework\TestCase\WebapiAbstract;
use Magento\Customer\Model\CustomerFactory;

class Import extends \Magento\Framework\App\Helper\AbstractHelper {

    //Import to magento database helper

    protected $_dataHelper;
    protected $_productRepository;
    protected $_productModel;
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

    //Attribute sets definition
    const COLORSIZE_ATTRIBUTES = 4;
    const COLORONLY_ATTRIBUTES = 12;
    const SIZEONLY_ATTRIBUTES = 13;
    const TIRES_ATTRIBUTES = 10;
    const SIMPLE_ATTRIBUTES = 14;
    const OIL_ACCESSORIES_ATTRIBUTES = 15;
    protected $MATRIX_LOG_FILE ="/var/import/importMatrix.log";


    //Configurable product vars
    protected $_usedConfigurableAttributeIds = [];
    protected $__configurableProductsData = [];

protected $allmatrixdata;
    protected $_objectManager;

    protected $state;

    protected $mdata;

    protected $categCollectionFactory;


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
    \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $cCollectionFactory
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
        $this->categCollectionFactory = $cCollectionFactory;

        $this->MATRIX_LOG_FILE =  $_SERVER["DOCUMENT_ROOT"].$this->MATRIX_LOG_FILE;

        if(!$this->state) {
            $this->setState();
        }


    }

    public function setState() {
        $this->state->setAreaCode('adminhtml');
    }

    public function getMatrixPreparedData() {

       $this->allmatrixdata = $this->_dataHelper->_getMatrixes();
       foreach($this->allmatrixdata as $data) {
           if($data['year'] == '2020') {
               $this->mdata = $data;
               $this->createColorSizeMatrix();
           }
       }

    }

    public function dispatchCreateMatrix() {

        if($this->mdata["AttributeSet"] === self::COLORSIZE_ATTRIBUTES) { $this->createColorSizeMatrix();}
        if($this->mdata["AttributeSet"] === self::COLORONLY_ATTRIBUTES) {$this->createColorOnlyMatrix();}
        if($this->mdata["AttributeSet"] === self::SIZEONLY_ATTRIBUTES) {$this->createSizeOnlyMatrix();}
        if($this->mdata["AttributeSet"] === self::TIRES_ATTRIBUTES) {$this->createTiresMatrix();}
        if($this->mdata["AttributeSet"] === self::OIL_ACCESSORIES_ATTRIBUTES) {$this->createOilAccessoriesMatrix();}
        if($this->mdata["AttributeSet"] === self::SIMPLE_ATTRIBUTES) {$this->createMatrixAsSimple();}

    }


    public function createSimpleTires($data) {

        $productRepository = $this->_objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');

        if(!isset($data['attributes'])) {
            return;
        }
        $attributes = $data['attributes'];
        $attrs = [];
        foreach($attributes as $attr) {

            if ($attr['name'] == 'Size') {
                $attrs['Size'] = strtoupper($attr['value']);

            } else {
               return;
            }
        }

        if(!isset($attrs['Size'])) {
            return;
        }


try {
    $product = $this->_objectManager->create('\Magento\Catalog\Model\Product');
    $product->setSku($data['magentoSku']);
    $product->setName(strtolower($data['name']));
    $product->setStoreId(0);
    $product->setWebsiteIds([1]);
    $product->setAttributeSetId(9); // Attribute set id
    $product->setLightspeedId($data['lightspeed_id']);
    $product->setVisibility(1);
    $product->setStatus(1); // Status on product enabled/ disabled 1/0
    $product->setWeight(0); // weight of product
    $product->setCategoryIds($this->setCategories($data));
    $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
    $product->setPrice($data['price']); // price of product
    $product->setLightspeedId($data['lightspeed_id']);

    $product->setStockData(
        array(
            'use_config_manage_stock' => 1,
            'manage_stock' => 1,
            'is_in_stock' => 1,
            'qty' => 0
        )
    );
    $product->setBrand($this->getAttributeValueByName(strtoupper($data['brand']), 'brand'));
    $product->setTireSize($this->getAttributeValueByName($attrs['Size'], 'tire_size'));
    $product->setDescription(strtolower($data['name']));
    $product->setShortDescription('');
    $product->setUrlKey($this->seo_friendly_url(strtolower($data['name'])));

    $images = $this->_imageHelper->getNewImages($data['lightspeed_id']);



    if ($images !== null) {

        if (is_array($images)) {
            foreach ($images as $imagea) {

                $image = $imagea['image'];
                $position = $imagea['position'];


                if (file_exists($image) && $position !== null) {

                    try {
                        $imagesize = getimagesize($image);
                    } catch (\Exception $e) {
                        return;
                    }

                    if (getimagesize($image) > 10) {

                        try {

                            if ($position == "0" || $position === null) {


                                $product->addImageToMediaGallery($image, array('image', 'thumbnail', 'small_image'), false, false);
                            } else {

                                $product->addImageToMediaGallery($image, null, false, false);
                            }

                            $product->save();

                        } catch (\Exception $e) {
                            echo "error at image ${image}";
                            die($e);

                        }


                    } else {
                        return;
                    }

                } else {
                    echo "error at image ${image} file does not exists";

                    return;
                }

                var_dump($images);

            }
        } else {

        }
    }} catch(\Exception $err) {
    die($err);
}

    $product->save();
   // $productRepository->save($product);
   // $productRepository->get($product->getSku());

    echo $product->getName() . "\n";


    }


    public function testCreateMatrix() {

        $this->getMatrixPreparedData();



    }

    public function createColorSizeMatrix($offset=0) {

        $mdata = $this->_dataHelper->_getMatrixes($offset);

        
        foreach($mdata as $data) {
            if($this->checkIfSimpleExists($data['SKU'], $data['LSID'])) {
                continue;
            }
        $productFactory = $this->_productFactory->create();
        $product = $this->_objectManager->create('\Magento\Catalog\Model\Product');

      //  $this->_imageHelper->getNewImages($data["LSID"]);

        $product->setSku($data["SKU"]);
        $product->setName($data["Name"]);
        $product->setWebsiteIds([1]);
        $product->setAttributeSetId(self::COLORSIZE_ATTRIBUTES);
        $product->setStatus(1);
        $product->setWeight(0);
        $product->setCategoryIds($this->setCategories($data));
        $product->setVisibility(4);
        $product->setTypeId('configurable');
        $product->setPrice(0);
        $product->setLightspeedId($data["LSID"]);
        $product = $this->setInventoryDataOnProduct($product);
        $product->setBrand($this->getAttributeValueByName($data["Brand"], 'brand'));
        if($data["Gender"] != "UNISEX") {
            $product->setGender($this->getAttributeValueByName($data["Gender"], 'gender'));
        }
        $product->setYear($this->getAttributeValueByName($data["Year"], 'year'));

        $product->setUrlKey($this->seo_friendly_url($data["Name"]."-".$data["SKU"]));
        $ids = [];
        $configurableProductsData1 = [];
        $configurableProductsData2 = [];
        $configurableProductsData3 = [];
        $notexists = 0;
        $this->_usedConfigurableAttributeIds = [];
        $configurableProductsData = [];

        $matrixItems = $this->_dataHelper->getSimplesIDForMatrix($data["LSID"]);

        var_dump($matrixItems);


        foreach($matrixItems as $sku) {
            $products = $this->_productFactory->create();
            $check = $products->loadByAttribute('sku', $sku);
            if(is_object($check)) {
                array_push($ids, $check->getId());



                if($check->getColor()) {
                    $configurableProductsData1[] = (int)$check->getColor();
                }
                if($check->getSize()) {
                    $configurableProductsData2[] = (int)$check->getSize();
                }
            } else {
                continue;
            }

        }

     //   var_dump($configurableProductsData1);
        $i = 0;
        $_configurableProductsData = [];
        foreach ($ids as $id) {
            $_configurableProductsData[$id] = [];
            if (isset($configurableProductsData1[$i]) && $configurableProductsData1[$i] != (int)1) {
                $_configurableProductsData[$id][$i] = [
                    'label' => '',
                    'attribute_id' => '93', //color
                    'value_index' => $configurableProductsData1[$i],
                    'is_percent' => '0',
                    'pricing_value' => '0',
                ];
                array_push($this->_usedConfigurableAttributeIds, 93);
            }


            if (isset($configurableProductsData2[$i]) && $configurableProductsData2[$i] != (int)1) {
                $_configurableProductsData[$id][$i] = [
                    'label' => '',
                    'attribute_id' => '144', //size
                    'value_index' => $configurableProductsData2[$i],
                    'is_percent' => '0',
                    'pricing_value' => '0',
                ];
                array_push($this->_usedConfigurableAttributeIds, 144);
            }

        }



        if (count($configurableProductsData1) > 0) {
            try {
                $product->getTypeInstance()
                    ->setUsedProductAttributeIds($this->_usedConfigurableAttributeIds, $product);

                $configurableAttributesData = $product->getTypeInstance()
                    ->getConfigurableAttributesAsArray($product);


                $ob = \Magento\Framework\App\ObjectManager::getInstance();
                $associatedProductIds = array_keys($_configurableProductsData);

                $product->setCanSaveConfigurableAttributes(true);
                $product->setAssociatedProductIds($associatedProductIds);
                $product->setConfigurableAttributesData($configurableAttributesData);
                $product->setConfigurableProductsData($_configurableProductsData);

                $optionsFactory = $ob->create('\Magento\ConfigurableProduct\Helper\Product\Options\Factory');
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


//var_dump($extensionConfigurableAttributes);


//            $configurableOptions = $optionsFactory->create($extensionConfigurableOptions);
  //            $extensionConfigurableAttributes->setConfigurableProductOptions($configurableOptions);
    //           $product->setExtensionAttributes($extensionConfigurableAttributes);

            } catch (\Exception $e) {
                echo $e;
continue;
         //   die($e);
            }




            $images = $this->_imageHelper->getNewImagesForMatrix($data['LSID']);

            var_dump($images);

//echo $data['LSID'];


            if($images !== null) {



                if (is_array($images)) {
                    foreach ($images as $imagea) {

                        $image = $imagea['image'];
                        $position = $imagea['position'];


                        if (file_exists($image) && $position !== null) {

                            try {
                                $imagesize = getimagesize($image);
                            } catch (\Exception $e) {
                               continue;
                            }

                            if (getimagesize($image) > 10) {

                                try {

                                    if ($position == "0" || $position === null) {


                                        $product->addImageToMediaGallery($image, array('image', 'thumbnail', 'small_image'), false, false);
                                    } else {

                                        $product->addImageToMediaGallery($image, null, false, false);
                                    }

                                   $product->save();

                                } catch (\Exception $e) {
                                    echo "error at image ${image}";
                                    echo $e;
                                    continue;
//continue;
                                }


                            } else {
                               continue;
                            }

                        } else {
                            echo "error at image ${image} file does not exists";

                            continue;
                        }

                        //var_dump($images);
                        echo " ...setting images\n";

                    }
                } else {

                }

                try {
                   $product->save();
                 //  $productRepository->save($product);

                } catch (\Exception $e) {
                  //  die($e);
                    continue;
                 //   var_dump($data);
                   // die($e);
                }

            } else {
                echo "error at image file does not exists for ".$data['LSID'];

            }




            $ids = [];
            $configurableProductsData1 = [];
            $configurableProductsData2 = [];
            $configurableProductsData3 = [];
            $this->_usedConfigurableAttributeIds = [];

           // unset($product);
           // unset($data);



        }}

        $offset = $offset + 100;

        $this->createColorSizeMatrix($offset);



    }

          public function createColorOnlyMatrix() {

        }

        public function createSizeOnlyMatrix() {

        }

        public function createTiresMatrix($offset=0) {
            $mdata = $this->_dataHelper->_getMatrixes($offset);


            foreach($mdata as $data) {
                if($this->checkIfSimpleExists($data['SKU'], $data['LSID'])) {
                    continue;
                }
                $productFactory = $this->_productFactory->create();
                $product = $this->_objectManager->create('\Magento\Catalog\Model\Product');

                //  $this->_imageHelper->getNewImages($data["LSID"]);

                $product->setSku($data["SKU"]);
                $product->setName($data["Name"]);
                $product->setWebsiteIds([1]);
                $product->setAttributeSetId(self::TIRES_ATTRIBUTES);
                $product->setStatus(1);
                $product->setWeight(0);
                $product->setCategoryIds($this->setCategories($data));
                $product->setVisibility(4);
                $product->setTypeId('configurable');
                $product->setPrice(0);
                $product->setLightspeedId($data["LSID"]);
                $product = $this->setInventoryDataOnProduct($product);
                $product->setBrand($this->getAttributeValueByName($data["Brand"], 'brand'));
                if($data["Gender"] != "UNISEX") {
                    $product->setGender($this->getAttributeValueByName($data["Gender"], 'gender'));
                }
                $product->setYear($this->getAttributeValueByName($data["Year"], 'year'));

                $product->setUrlKey($this->seo_friendly_url($data["Name"]."-".$data["SKU"]));
                $ids = [];
                $configurableProductsData1 = [];
                $configurableProductsData2 = [];
                $configurableProductsData3 = [];
                $notexists = 0;
                $this->_usedConfigurableAttributeIds = [];
                $configurableProductsData = [];

                $matrixItems = $this->_dataHelper->getSimplesIDForMatrix($data["LSID"]);


                foreach($matrixItems as $sku) {
                    $products = $this->_productFactory->create();
                    $check = $products->loadByAttribute('sku', $sku);
                    if(is_object($check)) {
                        array_push($ids, $check->getId());

                        if($check->getColor()) {
                            $configurableProductsData1[] = (int)$check->getColor();
                        }
                        if($check->getSize()) {
                            $configurableProductsData2[] = (int)$check->getSize();
                        }
                    }

                }

                $i = 0;
                $_configurableProductsData = [];
                foreach ($ids as $id) {
                    $_configurableProductsData[$id] = [];
                    if (isset($configurableProductsData1[$i]) && $configurableProductsData1[$i] != (int)1) {
                        $_configurableProductsData[$id][$i] = [
                            'label' => '',
                            'attribute_id' => '93', //color
                            'value_index' => $configurableProductsData1[$i],
                            'is_percent' => '0',
                            'pricing_value' => '0',
                        ];
                        array_push($this->_usedConfigurableAttributeIds, 93);
                    }


                    if (isset($configurableProductsData2[$i]) && $configurableProductsData2[$i] != (int)1) {
                        $_configurableProductsData[$id][$i] = [
                            'label' => '',
                            'attribute_id' => '144', //size
                            'value_index' => $configurableProductsData2[$i],
                            'is_percent' => '0',
                            'pricing_value' => '0',
                        ];
                        array_push($this->_usedConfigurableAttributeIds, 144);
                    }

                }

                if (count($configurableProductsData1) > 0) {
                    try {
                        $product->getTypeInstance()
                            ->setUsedProductAttributeIds($this->_usedConfigurableAttributeIds, $product);

                        $configurableAttributesData = $product->getTypeInstance()
                            ->getConfigurableAttributesAsArray($product);


                        $ob = \Magento\Framework\App\ObjectManager::getInstance();
                        $associatedProductIds = array_keys($_configurableProductsData);

                        $product->setCanSaveConfigurableAttributes(true);
                        $product->setAssociatedProductIds($associatedProductIds);
                        $product->setConfigurableAttributesData($configurableAttributesData);
                        $product->setConfigurableProductsData($_configurableProductsData);

                        $optionsFactory = $ob->create('\Magento\ConfigurableProduct\Helper\Product\Options\Factory');
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




                        $configurableOptions = $optionsFactory->create($extensionConfigurableOptions);
                       $extensionConfigurableAttributes->setConfigurableProductOptions($configurableOptions);
                        $product->setExtensionAttributes($extensionConfigurableAttributes);

                    } catch (\Exception $e) {
//continue;
                        die($e);
                    }




                    $images = $this->_imageHelper->getNewImagesForMatrix($data['LSID']);

                    var_dump($images);

//echo $data['LSID'];


                    if($images !== null) {



                        if (is_array($images)) {
                            foreach ($images as $imagea) {

                                $image = $imagea['image'];
                                $position = $imagea['position'];


                                if (file_exists($image) && $position !== null) {

                                    try {
                                        $imagesize = getimagesize($image);
                                    } catch (\Exception $e) {
                                        return;
                                    }

                                    if (getimagesize($image) > 10) {

                                        try {

                                            if ($position == "0" || $position === null) {


                                                $product->addImageToMediaGallery($image, array('image', 'thumbnail', 'small_image'), false, false);
                                            } else {

                                                $product->addImageToMediaGallery($image, null, false, false);
                                            }

                                            $product->save();

                                        } catch (\Exception $e) {
                                            echo "error at image ${image}";
                                            die($e);

                                        }


                                    } else {
                                        return;
                                    }

                                } else {
                                    echo "error at image ${image} file does not exists";


                                }

                                //var_dump($images);
                                echo " ...setting images\n";

                            }
                        } else {

                        }

                        try {
                            $product->save();
                            //   $productRepository->save($product);

                        } catch (\Exception $e) {
                            //   var_dump($data);
                            die($e);
                        }

                    } else {
                        echo "error at image file does not exists for ".$data['LSID'];

                    }




                    $ids = [];
                    $configurableProductsData1 = [];
                    $configurableProductsData2 = [];
                    $configurableProductsData3 = [];
                    $this->_usedConfigurableAttributeIds = [];

                    // unset($product);
                    // unset($data);



                }}

            $offset = $offset + 100;
            if($offset >= 7600) {
                die();
            }
            $this->createTiresMatrix($offset);
        }

        public function createOilAccessoriesMatrix() {

        //oil attribute = grade et format

        }

        public function createMatrixAsSimple() {

        }

        public function createSimpleProductColorSize($data) {

      /*  if(file_exists("/home/sp227/pub/media/import/" . $data['lightspeed_id'] . ".json")) {

            $j = file_get_contents("/home/sp227/pub/media/import/" . $data['lightspeed_id'] . ".json");
            var_dump(json_decode($j, true));

        }*/


        if($this->checkIfSimpleExists($data['magentoSku'], $data['lightspeed_id'])){
            //todo update routine
           $data['magentoSku'] = $data['magentoSku']."-2020";
        }

            $productRepository = $this->_objectManager->create('Magento\Catalog\Api\ProductRepositoryInterface');

if(!isset($data['attributes'])) {
    return;
}
            $attributes = $data['attributes'];
            $attrs = [];
            foreach($attributes as $attr) {
                if($attr['name'] == 'Color') {
                    $attrs['Color'] = $attr['value'];
                } else if($attr['name'] == 'Size') {
                    $attrs['Size'] = $attr['value'];
                }
            }




            $product = $this->_objectManager->create('\Magento\Catalog\Model\Product');
            $product->setSku($data['magentoSku']);
            $product->setName(strtolower($data['name']));
            $product->setStoreId(0);
            $product->setWebsiteIds([1]);
            $product->setAttributeSetId(self::COLORSIZE_ATTRIBUTES); // Attribute set id
            $product->setLightspeedId($data['lightspeed_id']);
            $product->setVisibility(1);
            $product->setStatus(1); // Status on product enabled/ disabled 1/0
            $product->setWeight(0); // weight of product
            $product->setCategoryIds($this->setCategories($data));
            $product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
            $product->setPrice($data['price']); // price of product
            $product->setLightspeedId($data['lightspeed_id']);

            $product->setStockData(
                array(
                    'use_config_manage_stock' => 1,
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => 0
                )
            );
            $product->setBrand($this->getAttributeValueByName(strtoupper($data['brand']), 'brand'));
            if(strtolower($data['gender']) == 'unisex') {

            } else {
                $product->setGender($this->getAttributeValueByName($data['gender'], 'gender'));
            }

            $product->setYear($this->getAttributeValueByName($data['year'], 'year'));
            if(isset($attrs['Color'])) {
                $value = $this->getAttributeValueByName($attrs['Color'], 'color');
                $product->setColor($value);
            }
            if(isset($attrs['Size'])) {
                $value = $this->getAttributeValueByName($attrs['Size'], 'size');
                $product->setSize($value);
            }
            $product->setDescription(strtolower($data['name']));
            $product->setShortDescription('');
            $product->setUrlKey($this->seo_friendly_url(strtolower($data['name'])));

            $images = $this->_imageHelper->getNewImages($data['lightspeed_id']);



            if($images !== null) {

                if (is_array($images)) {
                    foreach ($images as $imagea) {

                        $image = $imagea['image'];
                        $position = $imagea['position'];



                        if (file_exists($image) && $position !== null) {

                            try {
                                $imagesize = getimagesize($image);
                            } catch (\Exception $e) {
                                return;
                            }

                            if (getimagesize($image) > 10) {

                                try {

                                    if ($position =="0"  || $position === null) {



                                        $product->addImageToMediaGallery($image, array('image', 'thumbnail', 'small_image'), false, false);
                                    } else {

                                        $product->addImageToMediaGallery($image, null, false, false);
                                    }

                                    $product->save();

                                } catch (\Exception $e) {
                                    echo "error at image ${image}";
                                    return;

                                }



                            } else {
                                return;
                            }

                        } else {
                            echo "error at image ${image} file does not exists";

                            return;
                        }

                        var_dump($images);

                    }
                } else {

                }

                try {
                $product->save();
                $productRepository->save($product);
                $productRepository->get($product->getSku());

                echo $product->getName() . "\n";

                    } catch(\Exception $e) {
                    echo $e;
                    return;
                }
            }



        }

        public function checkIfSimpleExists($sku, $lightspeedid) {
            $products = $this->_productFactory->create();
            $check = $products->loadByAttribute('sku', $sku);
            $check2 = $products->loadByAttribute('lightspeed_id', $lightspeedid);

            if (is_object($check) || is_object($check2)) {
                return true;
            } else {

                $check = $products->loadByAttribute('lightspeed_id', $lightspeedid);
                if(is_object($check)) {
                    return true;
                }

                return false;
            }

        }


        public function fixSku($data) {

            $lsid = $data["lightspeed_id"];
            $products = $this->_productFactory->create();
            $product = $products->loadByAttribute('lightspeed_id', $lsid);
            if($product) {
                $product->setSku($data["magentoSku"]);
              try {
                  $product->save();
                  echo $product->getSku();
              }catch(\Exception $e) {
                  echo $e;
                  die();
              }
            }

        }


    public function setInventoryDataOnProduct($product) {
        $inventory = array(
            'use_config_manage_stock' => 0,
            'manage_stock' => 1,
            'is_in_stock' => 1,
        );
        $product->setStockData($inventory);
        return $product;
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
            ->addAttributeToFilter('name', strtoupper($sub[0]))
            ->setPageSize(1);

        if ($collection->count() > 0) {
            $id = $collection->getFirstItem()->getId();
        } else {
            $collection = $this->categCollectionFactory->create()
                ->addAttributeToFilter('name', strtoupper($sub[0]) . " DEV")
                ->setPageSize(1);
            if ($collection->count() > 0) {
                $id = $collection->getFirstItem()->getId();
            } else {


                if (false !== strpos(strtolower($sub[0]), "oil")) {
                    $id = 449;


                } else {

                    switch (strtolower(trim($sub[0]))) {

                        case "atv":
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

                          case "garage accessories dev":
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
                            $id=449;
                            break;
                        case "tires accessories":
                        case "tires accessories dev":
                            $id=375;
                            break;
                        case "fan gears":
                        case "fan gears dev":
                            $id=368;
                            break;
                        case "oil filters":
                        case "oil filters dev":
                            $id=732;
                            break;
                        case "air filters":
                        case "air filters dev":
                            $id=734;
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

                    echo $id;


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
                        echo $categs;
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

    public function getItemsForMatrix($matrixID) {
        return [];
    }

    //UTILITY FUNCTIONS

    public function getAttributeValueByName($name, $code)
    {

        $value = false;

        $name = str_replace("/", " ", $name);

        echo $name;
        echo $code;


        $attribute = $this->_productAttributeRepository->get($code)->getOptions();
        foreach ($attribute as $option) {


            if ($option->getLabel() == $name) {
                $value = $option->getValue();
                return $value;
            }
        }

        if ($value == false) {

            if (strlen($name) > 1) {

          /*     $optionLabel = $this->_optionLabelFactory->create();
               // $optionLabel->setStoreId(0);
                $optionLabel->setLabel($name);

                $option = $this->_optionFactory->create();
                $option->setLabel($optionLabel);
                $option->setStoreLabels([$optionLabel]);
                $option->setSortOrder(0);
                $option->setIsDefault(false);

                $this->_attributeOptionManagement->add(
                    \Magento\Catalog\Model\Product::ENTITY,
                    (int)$this->attributeByCode($code),
                    $option->getValue()
                );
          */  $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
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
    /* try {
         $attribute = $this->_productAttributeRepository->get($code)->getOptions();

         foreach ($attribute as $option) {


             if ($option->getLabel() == $name) {
                 $value = $option->getValue();
                 return $value;
             }
         }


         return $value;
     } catch(\Exception $e) {
            return;
     }*/
    }

    function getOptionId($atributeCode,$optionValue){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /*$attributeRepository = $objectManager->create('\Magento\Eav\Model\AttributeRepository');
        $attribute_id = $attributeRepository->get('catalog_product', $atributeCode)->getAttributeId();*/
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