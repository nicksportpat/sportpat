<?php
namespace Nick\Algoliabrands\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;

class Router implements RouterInterface {


    private $_actionFactory;
    private $_response;

    public function __construct(ActionFactory $actionFactory,
    ResponseInterface $response
)   {
        $this->_actionFactory = $actionFactory;
        $this->_response = $response;
    }


    public function match(RequestInterface $request) {

        $identifier = trim($request->getPathInfo(), '/');

        if(strpos($identifier, 'algoliabrands') !== false) {
            $request->setModuleName('algoliabrands');
            $request->setControllerName('index');
            $request->setActionName('index');
            $request->setParams(['brand_name' => $request->getParams('brand_name')]);
            $url = "";
            $this->_response->setRedirect($url, \Zend\Http\Response::STATUS_CODE_301);
            $request->setDispatched(true);
            return $this->_actionFactory->create(\Magento\Framework\App\Action\Redirect::class);
        }
        return null;

    }


}