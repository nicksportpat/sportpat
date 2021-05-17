<?php
namespace Nick\Algoliabrands\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;


use Nick\Algoliabrands\Helper\Data;

class Index extends Action {

    private $_pageFactory;
    private $_helper;
    private $_catalogDesign;

    public function __construct(Context $context,
PageFactory $pageFactory, Data $helper,
                                \Magento\Catalog\Model\Design $catalogDesign) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
        $this->_helper = $helper;
        $this->_catalogDesign = $catalogDesign;
    }



    public function execute()
    {
        $brandname = $this->getRequest()->getParam('brand_name', null);
        $this->_helper->query($brandname);

        return $this->_pageFactory->create();
     
    }



}