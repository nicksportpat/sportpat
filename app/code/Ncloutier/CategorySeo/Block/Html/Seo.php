<?php


namespace Ncloutier\CategorySeo\Block\Html;

class Seo extends \Magento\Framework\View\Element\Template
{

    /**
     * @return string
     */

    protected $_category;
    protected $_currcateg;
    protected $_categRepo;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,

        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->_category = $layerResolver;
        $this->_currcateg = $this->getCurrentCategory();
       


    }

    public function displaySeoBlock()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $catrepo = $objectManager->create('Magento\Catalog\Api\CategoryRepositoryInterface');
        $storeid = $storeManager->getStore()->getStoreId();
        $catid = $this->_currcateg->getId();
        $cat = $catrepo->get($catid);
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();



        $title = explode(" ", $this->getCurrentCategory()->getName());

        if(isset($title[0])) {
            $title = $title[0];
        } else {
            $title = $this->getCurrentCategory()->getName();
        }



        $sql = "SELECT * FROM amasty_amshopby_option_setting WHERE filter_code = 'attr_brand' AND url_alias LIKE '%".$title."%' AND store_id='".$storeid."'";
       // $csql = "SELECT * FROM catalog_category_flat_store_".$storeid." WHERE  entity_id = '".$this->getCurrentCategory()->getEntityId()."'";

        $cat = $catrepo->get($this->getCurrentCategory()->getEntityId(), 0);




        $brand = false;
        $rows = $connection->fetchAll($sql);

        if(is_object($cat) && !isset($rows[0])) {
            if($storeid == 1) {
                $text = $cat->getSeoTextEn();
            } else if($storeid == 2) {
                $text = $cat->getSeoText();
            }
        } else {


            if(isset($rows[0])) {
                $brand = true;
                $text = $rows[0]["description"];
            }
        }


        if ($brand === true) {
            $sql2 = "SELECT * FROM amasty_amshopby_option_setting WHERE filter_code = 'attr_brand' AND url_alias LIKE '%".$title."%' AND store_id='".$storeid."'";
            $rows2 = $connection->fetchAll($sql2);
            $image = $rows2[0]["image"];
            $imageurl = "/media/amasty/shopby/option_images/".$image;

        } else {
            $image = $this->getSeoImage();
            $imageurl = "/media/catalog/category/".$image;
        }

        $html = "";

        if($image != "") {

            $html = "<div class='category-view' style='background:#000;color:#fff;'><div class='row'><div class='col-xs-12 col-sm-5 flex-container image-container'><div class='category-image' ><img src='${imageurl}' alt='".$this->getCurrentCategory()->getName()."' title='".$this->getCurrentCategory()->getName()."' class='image' style='display:block;float:left;'></div></div><div class='col-xs-12 col-sm-5 col-sm-offset-1 flex-container info-container' style='margin-top:30px;margin-bottom:30px;margin-left:8%;margin-right:8%;'>    <div class='page-title-wrapper' style='display:block;float:left;'>
        <h1 class='page-title' id='page-title-heading' aria-labelledby='page-title-heading toolbar-amount'>
            <span class='base' data-ui-id='page-title-wrapper' style='display:block !important;float:left;font-weight:900; color:#fff;text-transform:uppercase;font-size:4.8rem;padding:10px;width:100%;'>".$this->getCurrentCategory()->getName()."</span>        </h1>
            <div class='category-description' style='padding:10px;font-size:1.4rem !important;'>
        ".addslashes($text)."   </div>
    </div>
</div></div></div>";
        } else {

            $html = "<div class='category-view' style='background:#000;color:#fff;'><div class='row'><div class='col-xs-12 col-sm-5 flex-container image-container'><div class='category-image' ></div></div><div class='col-xs-12 col-sm-5 col-sm-offset-1 flex-container info-container' style='margin-top:30px;margin-bottom:30px;margin-left:8%;margin-right:8%;'>    <div class='page-title-wrapper' style='display:block;float:left;'>
        <h1 class='page-title' id='page-title-heading' aria-labelledby='page-title-heading toolbar-amount'>
            <span class='base' data-ui-id='page-title-wrapper' style='display:block !important;float:left;font-weight:900; color:#fff;text-transform:uppercase;font-size:4.8rem;padding:10px;width:100%;'>".$this->getCurrentCategory()->getName()."</span>        </h1>
            <div class='category-description' style='padding:10px;font-size:1.4rem !important;'>
        ".addslashes($text)."   </div>
    </div>
</div></div></div>";
        }


        return $html;
    }


    public function getStoreId()
    {
        return 0;
    }

    public function getSeoText() {


        return $this->_currcateg->getData('seo_text');

    }

    public function getSeoImage() {
        return $this->_currcateg->getData('seo_image');
    }

    public function getCurrentCategory() {
        return $this->_category->get()->getCurrentCategory();
    }
}
