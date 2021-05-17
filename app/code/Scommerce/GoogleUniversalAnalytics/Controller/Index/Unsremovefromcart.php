<?php

namespace Scommerce\GoogleUniversalAnalytics\Controller\Index;

class Unsremovefromcart extends \Magento\Framework\App\Action\Action {

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
	protected $_coreSession;

    /**
     * @param \Magento\Framework\Session\SessionManagerInterface $coresession
     */
    public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $coresession
    ) {
        $this->_coreSession = $coresession;
		parent::__construct($context);
    }

    /**
     * return add to basket product data
     *
     *
     * @return string
     */
    public function execute() {
		if ($this->_coreSession->getProductOutBasket()){
			$this->_coreSession->unsProductOutBasket();
		}
    }

}
