<?php
namespace Nick\Canadapost\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class CanadaPostStandard extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
	\Magento\Shipping\Model\Carrier\CarrierInterface
{
	protected $_code = 'ca_std';
	protected $_rateResultFactory;
	protected $_rateMethodFactory;
	protected $_session;
	protected $_cart;

	public function __construct(
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
		\Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
		\Magento\Customer\Model\Session $session,
		\Magento\Checkout\Model\Cart $cart,
		array $data = []
	) {
		$this->_rateResultFactory = $rateResultFactory;
		$this->_rateMethodFactory = $rateMethodFactory;
		$this->_session = $session;
		$this->_cart = $cart;
		parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
	}


	public function getAllowedMethods()
	{
		return [$this->_code => $this->getConfigData('name')];
	}

	public function getQuoteTotal(){
		$total =  $this->_cart->getQuote()->getGrandTotal();
		if($this->_cart->getQuote()->getShippingAddress()){
			$total-=$this->_cart->getQuote()->getShippingAddress()->getShippingAmount();
			$total-=$this->_cart->getQuote()->getShippingAddress()->getTaxAmount();
		}
		return $total;
	}

	public function collectRates(RateRequest $request)
	{
		if (!$this->getConfigFlag('active')) {
			return false;
		}


		if($this->getConfigFlag('enable_hide_above') && $this->getConfigData('hide_above_amount')<=$request->getBaseSubtotalInclTax() ) return false;


		/** @var \Magento\Shipping\Model\Rate\Result $result */
		$result = $this->_rateResultFactory->create();

		/** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
		$method = $this->_rateMethodFactory->create();

		$method->setCarrier($this->_code);
		$method->setCarrierTitle($this->getConfigData('title'));

		$method->setMethod($this->_code);
		$method->setMethodTitle($this->getConfigData('name'));

		$amount = $this->getConfigData('price');

		$method->setPrice($amount);
		$method->setCost($amount);

		$result->append($method);

		return $result;
	}
}