<?php
namespace Sportpat\Sync\Model;

use Sportpat\Sync\Helper\Data;
use Sportpat\Sync\Helper\Images;
use Sportpat\Sync\Helper\Import;
use Sportpat\Sync\Helper\Update;
use Sportpat\Sync\Helper\Importer;
use Medoo\Medoo;

// Initialize


class Sync implements SyncInterface {


    protected $_data;
    protected $_images;
    protected $_import;
    protected $_syncType;
    protected $_update;
    protected $_importer;


    public function __construct(Data $data,
        Images $images, Import $import, Update $update, Importer $importer, $syncType="") {


        $this->_data = new Data();
        $this->_images = $images;
        $this->_import = $import;
        $this->_syncType = $syncType;
        $this->_update = $update;
        $this->_importer = $importer;



    }

    public function syncProducts($syncType, $output) {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->get('Magento\Framework\Registry')->register('isSecureArea', true);

            if($syncType == 'color-size') {
                $data = $this->_data->getAllSimplesColorSize();
                foreach($data as $d) {

                    $sku = $d['magentoSku'];
                    $exists = $this->checkIfExists($sku);

                    echo $exists . " : " . $sku;


                    if($exists == '1') {
                      echo $sku. " already exists"."\n";
                      continue;
                    } else if($exists == '0') {

                        $this->_import->createSimpleProductColorSize($d);

                    }
                }
            } else if($syncType == "new") {
                $this->_importer->initProductImport();
            }

            else if($syncType == 'translate') {
                $data = $this->_data->getAllSimplesColorSize();
                foreach($data as $d) {
                    $name = $d["name"];
                    $sku = $d['magentoSku'];
                   $product = $this->loadBySku($sku);
                   if($product) {
                       $this->_data->translate($name, $product);
                   }
                }
            }
            else if($syncType == 'translate-matrixes') {

                    $data = $this->_data->_getMatrixes();

                    foreach ($data as $d) {
                        $name = $d["Name"];
                        $sku = $d['SKU'];
                        $product = $this->loadBySku($sku);
                        if ($product) {
                            $this->_data->translateM($name, $product);
                        }
                    }

            }

            else if($syncType == 'update-names-fr') {
                $this->_update->setSimpleFrenchTranslations();
            }

            else if($syncType == 'update-matrix-names-fr') {
                $this->_update->setMatrixFrenchTranslations();
            }

            else if($syncType == 'color-only') {
                $data = $this->_data->getAllSimplesColorOnly();
              //  foreach($data as $d) {
                   // $this->_import->createSimpleProductColorOnly($d);
              //  }
            } else if($syncType == 'size-only') {
                $data = $this->_data->getAllSimplesSizeOnly();
              //  foreach($data as $d) {
                   // $this->_import->createSimpleProductColorSize($d);
              //  }
            } else if($syncType == 'tires') {
                $data = $this->_data->getAllSimplesTires();
                foreach($data as $d) {

                    $sku = $d['magentoSku'];
                    $exists = $this->checkIfExists($sku);

                    echo $exists . " : " . $sku;


                    if($exists == '1') {
                        echo $sku. " already exists"."\n";
                        continue;
                    } else if($exists == '0') {


                        $this->_import->createSimpleTires($d);
                    }
                }
            }
            else if($syncType == 'configurabletest') {

                $data = $this->_data->_getMatrixes();

                foreach($data as $d) {

                    $sku = $d['SKU'];
                    $exists = $this->checkIfExists($sku);

                    echo $exists . " : " . $sku;


                    if ($exists == '1') {
                        echo $sku . " already exists" . "\n";
                        continue;
                    } else if ($exists == '0') {
                        $this->_import->createColorSizeMatrix($d);
                    }
                }
            } else if($syncType == 'fixskus') {

                $data = $this->_data->getAllSimplesColorSize();
                foreach($data as $d) {
                    $this->_import->fixSku($d);
                }

            } else if($syncType == 'updatevis') {
                $this->_update->updateSimpleVisibility();
            } else if($syncType == 'updatedescriptions') {
                $this->_update->setDescriptions("fxr_2020.csv");
            } else if($syncType == 'new2020') {
                $this->_importer->initProductImport2020();
            } else if($syncType == 'dedupe') {
                $this->_update->dedupeNames();
        } else if($syncType== 'del2020') {
                $this->_update->deletebadskus();
            } else if($syncType == "fixgenders") {
                $this->_update->updateGenders();
            }

    }

    public function checkIfExists($sku) {

        $database = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'inventory',
            'server' => 'localhost',
            'username' => 'root',
            'password' => 'adv62062'
        ]);

        $query = "select * from products where sku = '".$sku."' LIMIT 1";
        $rows = $database->query($query)->fetchAll();
        $cnt = $database->query($query)->rowCount();

       if(!empty($rows)) {
           $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

           $productfactory = $objectManager->get('Magento\Catalog\Model\Product');
           $p = $productfactory->load($rows[0]["product_id"]);
try {
    $p->delete();
}catch(\Exception $e) {
   return 0;
}

           return 0;
       } else {
           return 0;
       }



    }

    public function loadBySku($sku) {

        $database = new Medoo([
            'database_type' => 'mysql',
            'database_name' => 'inventory',
            'server' => 'localhost',
            'username' => 'root',
            'password' => 'adv62062'
        ]);

        $query = "select * from products where sku = '".$sku."' LIMIT 1";
        $rows = $database->query($query)->fetchAll();
        $cnt = $database->query($query)->rowCount();


            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

           // $productfactory = $objectManager->get('Magento\Catalog\Model\Product');
        if(!empty($rows)) {
            $p = $rows[0]["product_id"];
            echo $p."\n\n";
           return $p;}

        else return false;


    }



}