<?php
namespace Nick\Algoliabrands\Helper;

use \Algolia\AlgoliaSearch\Helper\AlgoliaHelper;
use \Algolia\AlgoliaSearch\Helper\Data as AlgoliaData;


class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    private $client;
    private $algoliaHelper;
    private $algoliaData;

    public function __construct(AlgoliaHelper $helper, AlgoliaData $data) {

        $this->algoliaHelper = $helper;
        $this->client = $helper->getClient();
        $this->algoliaData = $data;


    }

    public function query($q, $params=[]) {
        $devIndex = "magento2_sportpat_dev_en_products";
        $liveIndex = "magento2_liveen_products";
        $results = $this->algoliaData->getSearchResult($q, 1);

        return $results;

    }





}