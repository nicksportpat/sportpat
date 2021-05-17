<?php

namespace Nick\Algoliasearch\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class MultiLanguageObserver implements ObserverInterface
{

    protected $_collection;
    protected $_categoryFactory;
    protected $_category;

    public function __construct( \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
                                 \Magento\Catalog\Model\CategoryFactory $categoryFactory,\Magento\Catalog\Model\Category $category)
    {
        // Observer initialization code...
        // You can use dependency injection to get any class this observer may need.
        $this->_collection = $collectionFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_category = $category;
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


        //var_dump($categories); die();
        $catids = $productRecord['categoryIds'];

        $i = 0;

        $t = [];

        foreach($catids as $id) {


           $t[$i] =  $this->getCategoryTranslations($id);
           $i ++;

        }

        $productRecord['categories_translations'] = $t;

       /* $productRecord['categories_t_root'] = [];

        $productRecord['categories_t_root'] = [
            'en' => $rootmod['categ_name']['en'],
            'fr' => $rootmod['categ_name']['fr']
        ];

        $productRecord['categories_t_level1'] = [];
        if(isset($mmod)) {

            $productRecord['categories_t_level1'] = [
                'en' => $rootmod['categ_name']['en'] . "///" . $mmod['categ_name']['en'],
                'fr' => $rootmod['categ_name']['fr'] . "///" . $mmod['categ_name']['fr']];
        }
        $productRecord['categories_t_level2'] = [];
        if(isset($smod)) {
            $productRecord['categories_t_level2'] =[
                'en' => $rootmod['categ_name']['en'] . "///" . $mmod['categ_name']['en'] . "///" . $smod['categ_name']['en'],
                'fr' => $rootmod['categ_name']['fr'] . "///" . $mmod['categ_name']['fr']. "///" . $smod['categ_name']['fr']];
        }*/




    }

    public function getCategoryTranslations($id) {

        $catEn = $this->_categoryFactory->create()->setStoreId(1)->load($id)->getName();
        $catFr = $this->_categoryFactory->create()->setStoreId(2)->load($id)->getName();

        $arr = [];

        $arr = [
            'en' => $catEn,
            'fr' => $catFr
        ];

        return $arr;


    }
}

