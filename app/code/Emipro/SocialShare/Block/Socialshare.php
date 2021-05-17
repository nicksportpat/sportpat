<?php

namespace Emipro\SocialShare\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Request\Http;

class Socialshare extends \Magento\Framework\View\Element\Template
{
    public $coreRegistry;
    public $scopeConfig;
    public $product;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Http $request,
        array $data = []
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->coreRegistry = $coreRegistry;
        $this->request = $request;
        parent::__construct($context, $data);
    }
    public function getProductImage()
    {
        $product = $this->coreRegistry->registry('currentproduct');//get current product
        if ($product) {
            return $product->getImage();
        }
    }
    public function getProductName()
    {
        $product = $this->coreRegistry->registry('currentproduct');//get current product
        if ($product) {
            return $product->getName();
        }
    }
}
