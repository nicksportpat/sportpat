<?php
namespace Sportpat\Menu\Block;

class Menu extends  \Magento\Framework\View\Element\Template {

    protected $_categoryFactory;
    protected $_category;
    protected $_categoryHelper;
    protected $_categoryRepository;
    protected $_catModel;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\Category $category,
        array $data = []
    )
    {
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryHelper = $categoryHelper;
        $this->_categoryRepository = $categoryRepository;
        $this->_catModel = $category;
        parent::__construct($context, $data);
    }

    public function getCategoryHelper() {

        return $this->_categoryHelper;

    }

    public function getCategoryByID($id) {

        $category = $this->_categoryFactory->create();
        return $category->load($id);

    }

    public function getChildCategories($category) {

        return $category->getChildren();

    }


    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
       // $parentcategories = $this->_categoryRepository->get(2);
       // $categories = $parentcategories->getChildrenCategories();
        //return $categories;
        return $this->_categoryHelper->getStoreCategories($sorted , $asCollection, $toLoad);
    }


    public function getMainCategories() {

        $categories = $this->getStoreCategories();
        $mainmenuArray = [];


        $i = 0;

        foreach($categories as $category) {




            $categoryHasChilds = $this->getChildCategories($category);
            $numchilds = count($categoryHasChilds);


            if($category->getData('is_active') == 1 && $numchilds > 0) {

                if(false === strpos($category->getName(), "DEV") &&
                    false === strpos($category->getName(), "ELECTRICAL") &&
                    false === strpos($category->getName(), "AUTO") &&
                    false === strpos($category->getName(), "GEAR") &&
                    false === strpos($category->getName(), "GARAGE") &&
                    false === strpos($category->getName(), "FAN") &&
                    false === strpos($category->getName(), "BAGS") &&
                    false === strpos($category->getName(), "OIL") &&
                    false === strpos($category->getName(), "COMMUNICATION") &&
                    false === strpos($category->getName(), "CHAIN") &&
                    false === strpos($category->getName(), "AIR FILTERS") &&
                    false === strpos($category->getName(), "HUILE") &&
                    false === strpos($category->getName(), "CHAINE") &&
                    false === strpos($category->getName(), "SAC") &&
                    false === strpos($category->getName(), "FILTRE") &&
                    false === strpos($category->getName(), "STICKERS") && false === strpos($category->getName(), "PROMO") && false === strpos($category->getName(), "CHAUFFANT") ) {
                    $mainmenuArray[] = ["id"=>$category->getId(), "name" => $category->getName(), "url" => $category->getUrlKey()];
                }


            }





        }

        return $mainmenuArray;



    }

    function getCategorySubTitles($id) {
        $cat = $this->_catModel->setStore(0)->load($id);


        $categories = $this->_categoryFactory->create()
            ->setStore(1)->load($id);
        $childs = $categories->getChildrenCategories();
        return $childs;

    }

    function getSubCategoryHtml($parentid, $parentName, $subname) {

        $subcateg = $this->getCategorySubTitles($parentid);

        $html = "";
        foreach($subcateg as $categ) {
            $count = $categ->getProductCount();
            if( $count == 0) {continue;}

            if($categ["name"] == "STICKERS" || $categ["name"] == "FAN GEARS DEV " || $categ["name"] == "ARTICLE PROMO"

            ) {
                continue;
            }

            if(strtoupper($subname) == "KIDS" || strtoupper(str_replace(" ","",$subname)) == "ENFANTS") {
                $liclass = "col-lg-12";
                $subliclass = "col-lg-3";
            }
            else {
                $liclass = "col-lg-6";
                $subliclass = "col-lg-5";
            }

            $menuname1 = str_replace("DEV", "", $categ["name"]);
            $fullname = $this->getSubCategoryImage($parentName, $menuname1, strtoupper($subname));

            $html .= '<li class="'.$subliclass.' col-md-5 col-xs-12 float-left"><a href="'.$categ->getUrl().'"><span class="submenu-icon" style="padding-right:3px;"><img src="/media/menu/'.$fullname.'" width="48" height="48"  style=" padding:2px;max-height:48px !important;width:48px;height:48px;" /></span>'.$menuname1.'</a></li>';
        }

        return $html;

    }

    function getSubCategoryImage($parentName, $catName, $subname="") {
        $menuname = strtoupper(str_replace(" ","",$parentName));
        if($subname == "MENS " || $subname == "WOMENS " || $subname == "KIDS" || $subname == "HOMMES " || $subname == "FEMMES" || $subname == "ENFANTS") {
            $menuname = $menuname." ".$subname;

        }




        $menuimagename = str_replace(" ", "", strtolower($catName));
        $menuimagename = str_replace("dev", "", $menuimagename);

       // echo $menuimagename;

        $ext = "";

      //  echo strtolower(str_replace(" ","",$menuname))."-"."$menuimagename".$ext;

        if(file_exists('/home/sportpat/pub/media/menu/'.strtolower(str_replace(" ","",$menuname))."-"."$menuimagename".".jpg")) {
            $ext = ".jpg";
            return strtolower(str_replace(" ","",$menuname))."-"."$menuimagename".$ext;
        }

        if(file_exists('/home/sportpat/pub/media/menu/'.strtolower(str_replace(" ","",$menuname))."-"."$menuimagename".".png")) {
            $ext = ".jpg";
            return strtolower(str_replace(" ","",$menuname))."-"."$menuimagename".$ext;
        }

        if($ext == "") {



            if(file_exists('/home/sportpat/pub/media/menu/'.$menuimagename.".jpg")) {
                $ext = ".jpg";
                return $menuimagename.$ext;
            }

            else if(file_exists('/home/sportpat/pub/media/menu/'.$menuimagename.".png")) {
                $ext = ".png";
                return $menuimagename.$ext;
            } else {
              //  return "noimage.png";
                return $menuimagename.$ext;
            }

        }

        if(file_exists('/home/sportpat/pub/media/menu/'.strtolower(str_replace(" ","",$menuname))."-"."$menuimagename".".jpg")) {
            $ext = ".jpg";
            return strtolower(str_replace(" ","",$menuname))."-"."$menuimagename".$ext;
        }

        if(file_exists('/home/sportpat/pub/media/menu/'.strtolower(str_replace(" ","",$menuname))."-"."$menuimagename".".png")) {
            $ext = ".jpg";
            return strtolower(str_replace(" ","",$menuname))."-"."$menuimagename".$ext;
        }

        //return "noimage.png";
        return $menuimagename.$ext;

    }





}