<?php
namespace Sportpat\OrderSync\Helper;
use LightspeedHQ\Retail\RetailClient;

class SyncData extends \Magento\Framework\App\Helper\AbstractHelper {


    private $client;
    private $_configuration;
    private $logfile = "/data/home/sportpat/var/log/OrderSync.log";
    private $objectManager;
    private $taxClass;
    private $storeManager;
    private $db;


    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;
    protected $quoteItems;

    private $customer;
    private $country;
    private $date;

    protected $_orderIds = [];
    private $_state;
//protected $logfile;

    public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
\Magento\Store\Model\StoreManagerInterface $storeManager,
\Magento\Framework\App\ResourceConnection $resource,
\Magento\Framework\Stdlib\DateTime\DateTime $date,
\Magento\Customer\Model\Customer $customer,
\Magento\Directory\Model\Country $country,
        \Magento\Framework\App\State $state
) {
        $this->_configuration = [
            'account_id' => "142989",
            'client_id'=> "c8f5b0d84c9b5c077b56aa73369b01d842734bb68bbe27655aeb1bb090da6693",
            'client_secret'=> "b9491a7d8c601a6d902b9feef6f0ed415a93898ab653c0ab1605864667af0440",
            'refresh_token' => 'ff77dd2c302961fcf3f1ef8f18102039f2412079',
        ];

        $this->getClient();

        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->db = $resource;
        $this->customer = $customer;
        $this->country = $country;
        $this->date = $date;
        $this->_state = $state;
        if(!$this->_state) {
            $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND); // or \Magento\Framework\App\Area::AREA_ADMINHTML, depending on your needs
        }



    }

    public function getTaxIDByProvince($prov) {

        $alberta = 2;
        $bc = 3;
        $ipe = 4;
        $mb = 5;
        $nb = 6;
        $ns = 7;
        $nun = 8;
        $on = 9;
        $qc = 10;
        $sk = 11;
        $tnl = 12;
        $tno = 13;
        $yu = 14;
        $usa = 15;

        if ($prov == 'alberta') return $alberta;
        if ($prov == 'prince edward island') return $ipe;
        if ($prov == 'manitoba') return $mb;
        if ($prov == 'new brunswick') return $nb;
        if ($prov == 'nova scotia') return $ns;
        if ($prov == 'nunavut') return $nun;
        if ($prov == 'ontario') return $on;
        if ($prov == 'quebec') return $qc;
        if ($prov == 'saskatchewan') return $sk;
        if ($prov == 'newfoundland and labrador') return $tnl;
        if ($prov == 'northwest territories') return $tno;
        if ($prov == 'yukon') return $yu;
        if ($prov == 'british-columbia') return $bc;


    }

    public function getClient():RetailClient {

        $this->client = new RetailClient($this->_configuration["account_id"],
            $this->_configuration["refresh_token"],
            $this->_configuration["client_id"],
            $this->_configuration["client_secret"]
        );

        return $this->client;

    }

    public function getRequest($type="", $params=[]) {

        $response = $this->client->get($type, ['query' => $params]);
        return json_decode($response->getBody(), true);

    }

    public function postRequest($type="", $params=[]) {

        $response = $this->client->post($type, ['json' => $params]);
        return json_decode($response->getBody(), true);

    }

    public function putRequest($type="", $params=[]) {

        $response = $this->client->put($type, ['query' => $params]);
        return json_decode($response->getBody(), true);

    }

    public function log($data) {

        $currentdata = "";
/*        if(file_exists($this->logfile)) {
            $filetime = filemtime($this->logfile);
            $filedate = date("d", $filetime);
            $now = date("d");
            if($filedate < $now) {
                unset($this->logfile);
            } else {
                $currentdata = file_get_contents($this->logfile);
            }
        }

        $fh = fopen($this->logfile, "rw+");
        $data = $currentdata . " \n" . $data;
        fwrite($fh, $data);
        fclose($fh);
*/
    }

    public function syncOrderToLS($order) {
        error_reporting(E_ALL);
        ini_set('display_errors',1);
        try {

            $orderData = $this->formatOrderData($order);

            $dataArray = $this->postRequest('Sale',$orderData);
            $orderPost = $dataArray["Sale"];

            $orderLightspeedId = null;

            if (isset($orderPost['saleID'])) {
                $orderLightspeedId = $orderPost['saleID'];
                $this->log("Sync of order {$order->getIncrementId()} completed with order lsid of {$orderLightspeedId}");
                $this->setSyncSuccessDetails("Sync of order {$order->getIncrementId()} completed with order lsid of {$orderLightspeedId}", $order->getIncrementId(), $orderLightspeedId);

                list($isSpecial,$specialDatas) = $this->isSpecialOrder($order);

                if($isSpecial){

                    try{

                        $notes = "";

                        foreach($specialDatas as $specialData){
                            $notes .= $specialData['name']." => ".$specialData['qty']." => ".$specialData['supplier'].PHP_EOL;
                        }

                        $quoteData = array(
                            "issueDate"=>date('c',  strtotime($order->getCreatedAt())),
                            "notes" =>$notes,
                            "archived"=>false,
                            "employeeID"=>22,
                            "saleID"=>$orderLightspeedId
                        );

                        //Create Quote
                        $this->log("Creating quote for {$order->getIncrementId()}");
                        $quoteDataArray = $this->postRequest('Quote',$quoteData);

                        $quotePost = $quoteDataArray;
                        //$quoteAttributes = $quoteDataArray['attributes'];
                        //$this->_remainingBucket = $quoteAttributes['free_bucket'];


                        if (isset($quotePost['quoteID'])) {

                            $this->log("Creating quote for {$order->getIncrementId()} completed");

                            //Update order
                            $quoteLightspeedId = $quotePost['quoteID'];

                            $orderUpdate = array(
                                "saleID" =>$orderLightspeedId,
                                "quoteID"=>$quoteLightspeedId
                            );

                            $this->log("Updating order {$order->getIncrementId()} with Quote $quoteLightspeedId");
                            $orderUpdateDataArray = $this->postRequest('Quote',$orderUpdate);
                            //$orderUpdateAttributes = $orderUpdateDataArray['attributes'];
                            //$this->_remainingBucket = $orderUpdateAttributes['free_bucket'];

                            $this->log("Updating order {$order->getIncrementId()} with Quote $quoteLightspeedId completed");

                        }else{
                            $this->log("Creating quote for {$order->getIncrementId()} failed");
                        }


                    }catch(Exception $ex){
                        $this->log("Creation of quote for order {$order->getIncrementId()} failed");
                    }
                }

            } else {
                $this->log("Sync of order {$order->getIncrementId()} failed");
            }

            return $orderLightspeedId;

        }catch(\Exception $e){

            $this->log("Error Syncing order {$order->getIncrementId()} : <br/>".$e->getMessage().$e->getFile().$e->getLine());
        }

        return false;

    }

    public function isOrderInvoiced($order){

        $invoiced = $order->getInvoiceCollection()->count()>0;
        if(!$invoiced) return false;

        /** @var $invoice \Magento\Sales\Model\Order\Invoice */
        $invoice = $order->getInvoiceCollection()->getFirstItem();

        return $order->getGrandTotal()==$invoice->getGrandTotal();
    }

    public function existOnLightSpeed($order){


        $service= "Sale";
        $query = array(
            //'load_relations' => 'all',
            "referenceNumberSource"=>"Magento",
            'referenceNumber'=>(string)$order->getIncrementId()
        );

        $result = $this->getRequest($service, $query);



        $count = @$result['@attributes']['count'];

        return $count;
    }


    public function formatOrderData($order) {

        $taxCategoryID = 0;
        $orderDate = date('c',  strtotime($order->getCreatedAt()));
        $salesLines = $this->_getSaleOrderLines($order,$orderDate);

        if($order->getShippingAddress()) {
            $address = $order->getShippingAddress();
        } else {
            $address = $order->getBillingAddress();
        }

        $street = $address->getStreet();
        $street1 = '';
        if(isset($street[1])) {
            $street1 = $street[1];
        }

//var_dump($address->getRegion());

        $orderData = array(
            'completed'     => 'true',
            'archived'      => 'false',
            'voided'        => 'false',
            'isTaxInclusive'=> 'true',
            'taxCategoryID' => $this->getTaxIDByProvince(strtolower($address->getRegion())),
            'referenceNumber' => $order->getIncrementId(),
            'referenceNumberSource'=>'Magento',
            'customerID'    => $this->getCustomerId($order),
            'employeeID'    => 22,
            'shopID'        => 1,
            'registerID'    => 2,
            'ShipTo'        => array(
                'shipped'   => $order->hasShipments()?'true':'false',
                'shipNote'  => $order->getShippingDescription(),
                'title'     => $address->getPrefix(),
                'firstName' => $address->getFirstname(),
                'lastName' => $address->getLastname(),
                'company'   => $address->getCompany(),
                'Contact'   => array(
                    'Addresses' => array(
                        'ContactAddress' => array(
                            'address1'  => $street[0],
                            'address2'  => $street1,
                            'city'  => $address->getCity(),
                            'state'  => $address->getRegion(),
                            'zip'  => $address->getPostcode(),
                            'country'   => $this->country->loadByCode($address->getCountryId())->getName(),

                        )
                    ),
                    'Emails' => array(
                        'ContactEmail' => array(
                            'address' => $order->getCustomerEmail(),
                            'useType' => 'Primary'
                        )
                    )
                )
            ),
            'SalePayments'  => array(
                'SalePayment'   => array(
                    array(
                        'amount'    => (float)$order->getBaseGrandTotal(),
                        'createTime'=> $orderDate,
                        'registerID'=> 2,
                        'PaymentType' => array(
                            'paymentTypeID'      => 9,
                        )
                    )
                )
            ),
            'orderedDate'   => $orderDate,
            'timeStamp'     => $orderDate,
            'SaleLines'    => array(
                'SaleLine' => $salesLines,
            )

        );

        if($order->getCustomerId()) $orderData['customerID'] = $this->getCustomerId($order);
        else $orderData['customerID'] = $this->getGuestCustomerId($order);

        list($isSpecial,$specialDatas) = $this->isSpecialOrder($order);

        if($isSpecial){
            $orderData['completed']=false;
        }

        $this->log(json_encode($orderData));

        return $orderData;

    }

    protected function isSpecialOrder($order){
        $isSpecial = false;
        $data = array();

        $connection = $this->db->getConnection();


        /** @var $item \Magento\Sales\Model\Order\Item */
        foreach($order->getItems() as $item){
            $productId = $item->getProductId();
            $query = "SELECT * FROM amasty_multiinventory_warehouse_item WHERE product_id='".$productId."'";
            $rows = $connection->fetchAll($query);
            $warehouseid = 0;
            $lsqty = 0;
            $wqty=0;

            if(!empty($rows)) {

                foreach ($rows as $row) {
                    if ($row["warehouse_id"] == 3) {
                        $warehouseid = 3;
                        $lsqty = $row["qty"];
                    } else {
                        $warehouseid = $row["warehouse_id"];
                        $wqty = $row["qty"];
                    }


                }

                $squery = "SELECT * FROM amasty_multiinventory_warehouse WHERE warehouse_id = {$warehouseid}";
                $srows = $connection->fetchAll($squery);


                if ($lsqty == 0 || $lsqty < $item->getQty()) {
                    $isSpecial = true;
                    $data[] = array(
                        "name" => $item->getSku() . " - " . $item->getName(),
                        "qty" => $wqty,
                        "supplier" => $srows[0]["title"],
                    );
                }

            }

        }

        return array($isSpecial,$data);
    }

    protected function getGuestCustomerId($order){

        $billingAddress = $order->getBillingAddress();
        $country = $this->country->load($billingAddress->getCountry(),'code')->getName();

        $customerData = array(
            'taxCategoryID'	=>	159,
            'timeStamp' => date("c"),
            'firstName' => $billingAddress->getFirstname(),
            'lastName'  => $billingAddress->getLastname(),
            'dob'       => $order->getCustomerDob(),
            'Contact'   => array(
                'Phones'    => array(
                    'ContactPhone' => array(
                        'number' => $billingAddress->getTelephone(),
                        'useType' =>  "Home"
                    )
                ),
                'Emails'    => array(
                    'ContactEmail' => array(
                        'address' => $order->getCustomerEmail(),
                        "useType" => "Primary"
                    )
                ),
                'Addresses'    => array(
                    'ContactAddress' => array(
                        'address1'  => $billingAddress->getStreet()[0],
                        'address2'  => "",
                        'city'      => $billingAddress->getCity(),
                        'state'     => $billingAddress->getRegion(),
                        'zip'       => $billingAddress->getPostcode(),
                        'country'   => $country,
                        'countryCode' => $billingAddress->getCountry()
                    )
                ),
            )
        );


        $encodedData = json_encode($customerData);



        //find by data
        $query= array(
            'load_relations' => '["Contact"]',
            "Contact.email"=>$order->getCustomerEmail()
        );

        $results = $this->getRequest("Customer",$query);



        if($results["@attributes"]["count"] > 0) {

            $customers = $results["Customer"];

            if (!@$customers[0]) $customers = array($customers);


            //Find by email, phone and address
            foreach ($customers as $customer) {

                $contactData = array(
                    "Phones" => @$customer["Contact"]['Phones'],
                    "Emails" => @$customer["Contact"]['Emails'],
                    "Addresses" => @$customer["Contact"]['Addresses']
                );

                $result = @$customerData["Contact"] == @$contactData;
                if ($result) {
                    return $customer["customerID"];
                }

            }


            //fallback find by email
            foreach ($customers as $customer) {
                $customerEmail = @$customer["Contact"]["Emails"]["ContactEmail"]["address"];
                if ($customerEmail == $order->getCustomerEmail()) {
                    return $customer["customerID"];
                }
            }

        }

        //not found any, create
        $restClient = $this->postRequest("Customer", $customerData);
        $customerPost = $restClient;

        if ($lightSpeedId = $customerPost['Customer']['customerID']) {
            return $lightSpeedId;
        } else return 0;


    }

    protected function getCustomerId($order)
    {
        $customerId = $order->getCustomerId();

        if(is_null($customerId)) {
            return 0;
        }

        $customer = $this->customer->load($customerId);

        if($customer->getLightspeedId()) {
            return $customer->getLightspeedId();
        }

        $billingAddress = $customer->getDefaultShippingAddress();
        if(!$billingAddress) $billingAddress = $order->getShippingAddress();

        try {
            $country = $this->country->loadByCode($billingAddress->getCountry())->getName();
        }catch(\Exception $ex){
            $country = $this->country->load($billingAddress->getCountry(),'code')->getName();
        }

        $customerData = array(
            'taxCategoryID'	=>	159,
            'timeStamp' => date("c"),
            'firstName' => $customer->getFirstname(),
            'lastName'  => $customer->getLastname(),
            'dob'       => $customer->getDob(),
            'title'     => $customer->getTitle(),
            'company'   => $customer->getCompany(),
            'Contact'   => array(
                'Phones'    => array(
                    'ContactPhone' => array(
                        'number' => $billingAddress->getTelephone(),
                        'useType' =>  "Home"
                    )
                ),
                'Emails'    => array(
                    'ContactEmail' => array(
                        'address' => $customer->getEmail(),
                        "useType" => "Primary"
                    )
                ),
                'Addresses'    => array(
                    'ContactAddress' => array(
                        'address1'  => $billingAddress->getStreet()[0],
                        'address2'  => "",
                        'city'      => $billingAddress->getCity(),
                        'state'     => $billingAddress->getRegion(),
                        'zip'       => $billingAddress->getPostcode(),
                        'country'   => $country,
                        'countryCode' => $billingAddress->getCountry()
                    )
                ),
            )
        );

        $restClient = $this->postRequest("Customer",$customerData);

        $customerPost = $restClient;

        if(isset($customerPost['Customer']['customerID'])){
            $customer
                ->setLightspeedId($customerPost['Customer']['customerID'])
                ->save();

            $this->log("Customer {$customerPost['Customer']['customerID']} created");
        }else{
            $this->log("Customer creation failed");
        }

        return $customer->getLightspeedId();
    }

    protected function _getSaleOrderLines($order, $orderDate)
    {


        $items = $order->getAllVisibleItems();
        $saleOrderLines = array();
        $this->_highestTax = 0;


        /** @var $item \Magento\Sales\Model\Order\Item */
        foreach($items as $item ){

            $sku = $item->getSku();
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productObject = $objectManager->get('Magento\Catalog\Model\Product');
            $product = $productObject->loadByAttribute('sku', $sku);

            $lightspeedId = $product->getData('lightspeed_id') ?? 0;
            $taxClassId = 1;
            if($item->getTaxPercent() > $this->_highestTax) {
                $this->_highestTax = $item->getTaxPercent();
            }



            $orderLine = array(
                'unitQuantity'    => (float)$item->getQtyOrdered(),
                'unitPrice'=> (float)$item->getBasePrice(),
                'itemID'      => $lightspeedId,
                'createTime'  => $orderDate,
                'discountAmount' => (float)$item->getBaseDiscountAmount(),
                'timeStamp'   => $orderDate,
                'tax'         => ($item->getBaseTaxAmount() > 0)?'true':'false',
                'taxClassID' => $taxClassId,
                'avgCost'   => (float)$item->getBasePrice(),
                'fifoCost' => (float)$item->getBasePrice(),
            );

            $simpleSku = @$item->getProductOptions()['simple_sku'];
            if($simpleSku){

                /** @var $repository \Magento\Catalog\Api\ProductRepositoryInterface */
                $repository = $this->objectManager->create("Magento\Catalog\Api\ProductRepositoryInterface");

                $simpleProduct = $repository->get($simpleSku);
                $simplePrice = $simpleProduct->getPrice();


            }


            $saleOrderLines[] = $orderLine;
        }




        $shippingCost = $order->getBaseShippingAmount() !== null ? $order->getBaseShippingAmount():0;

        $shippingItemId = 39000;
        $itemID = !empty($shippingItemId)?(int)$shippingItemId:0;

        $saleOrderLines[] = array(
            'unitQuantity'  => 1,
            'unitPrice' => (float)$shippingCost,
            'itemID'    => $itemID,
            'createTime'  => $orderDate,
            'timeStamp'   => $orderDate,
            'tax'         => ($order->getBaseShippingTaxAmount() > 0)?'true':'false',
            'taxClassID' => 0,
            'avgCost'   => (float)$shippingCost,
            'fifoCost' => (float)$shippingCost,
        );


        return $saleOrderLines;

    }

    public function getPendingOrdersToSync() {

        $this->_orderIds = [];
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM sportpat_order_sync_syncedorder WHERE status = 1";
        $results = $conn->fetchAll($sql);
        foreach($results as $order) {

            $this->_orderIds[] = $order["magento_orderid"];
            $sql2 = "UPDATE sportpat_order_sync_syncedorder SET status = 4 WHERE magento_orderid = '".$order["magento_orderid"]."'";
            $conn->query($sql2);

            echo $order["magento_orderid"]. "set as PROCESSING";

        }

        if(!empty($this->_orderIds)) {

            for($i=0; $i<count($this->_orderIds); $i++) {
                $order = $this->fetchOrderByMagentoOrderId($this->_orderIds[$i]);
               // var_dump($order->getData());

                $this->syncOrderToLS($order);
            }

        }

    }

    public function fetchOrderByMagentoOrderId($orderid) {

        $orderFactory = $this->objectManager->create('Magento\Sales\Model\Order');
        $order = $orderFactory->loadByIncrementId($orderid);
        return $order;

    }

    public function setSyncSuccessDetails($details, $orderid, $lsoid) {
        $sql = "UPDATE sportpat_order_sync_syncedorder SET status = 2 WHERE magento_orderid = '".$orderid."'";
        $sql2 = "UPDATE sportpat_order_sync_syncedorder SET details='".json_encode($details)."' WHERE magento_orderid = '".$orderid."'";
        $conn = $this->db->getConnection();
        $conn->query($sql2);
        $conn->query($sql);
        $sql3 = "UPDATE sportpat_order_sync_syncedorder SET lightspeed_orderid = '".$lsoid."' WHERE magento_orderid = '".$orderid."'";
        $conn->query($sql3);

    }

    public function setSyncError($orderid) {
        $sql = "UPDATE sportpat_order_sync_syncedorder SET status = 1 WHERE magento_orderid = '".$orderid."'";
        $conn = $this->db->getConnection();
        $conn->query($sql);
    }


}
