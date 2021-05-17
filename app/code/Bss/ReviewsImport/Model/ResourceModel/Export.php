<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_ReviewsImport
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ReviewsImport\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class Export
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $readAdapter;

    /**
     * @var array
     */
    protected $tableNames = [];

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Export constructor.
     * @param ResourceConnection $resourceConnection
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->timezone = $timezone;
        $this->readAdapter = $this->resourceConnection->getConnection('core_read');
    }

    /**
     * @return \Zend_Db_Statement_Interface
     */
    public function getReviewsTable()
    {
        $select = $this->readAdapter->select()
            ->from(
                ['main_table' => $this->getTableName('review')],
                [
                    'review_id',
                    'created_at',
                    'entity_pk_value'
                ]
            )
            ->join(
                ['rd' => $this->getTableName('review_detail')],
                'main_table.review_id = rd.review_id',
                ['store_id', 'title', 'detail', 'nickname', 'customer_id']
            )
            ->join(
                ['store' => $this->getTableName('store')],
                'store.store_id = rd.store_id',
                ['code']
            )
            ->joinLeft(
                ['rs' => $this->getTableName('review_status')],
                'main_table.status_id = rs.status_id',
                ['status_code']
            )
            ->joinLeft(
                ['product' => $this->getTableName('catalog_product_entity')],
                'main_table.entity_pk_value = product.entity_id',
                ['sku']
            )
            ->order(['main_table.review_id']);
        $review = $this->readAdapter->query($select);
        return $review;
    }

    /**
     * @param $reviewId
     * @return \Zend_Db_Statement_Interface
     */
    protected function getStoreViewCode($reviewId)
    {
        $select = $this->readAdapter->select()
            ->from(
                ['main_table' => $this->getTableName('review_store')],
                [
                    'review_id',
                ]
            )->join(
                ['store' => $this->getTableName('store')],
                'store.store_id = main_table.store_id',
                ['code', 'store_id']
            )->where("main_table.review_id = $reviewId")
            -> where("store.store_id > 0");
        $stores = $this->readAdapter->query($select);
        return $stores;
    }

    /**
     * @param int $reviewId
     * @return \Zend_Db_Statement_Interface
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getRatingOption($reviewId)
    {
        // phpcs:disable
        $select = $this->readAdapter->select()
            ->from(
                ['rov' => $this->getTableName('rating_option_vote')],
                [
                    'rating_id',
                    'value',
                ]
            )->joinLeft(
                ['rating' => $this->getTableName('rating')],
                'rating.rating_id=rov.rating_id',
                ['rating_code']
            )->where("rov.review_id = $reviewId")
            ->group('rov.rating_id');

        // phpcs:enable
        $rating = $this->readAdapter->query($select);
        return $rating;
    }

    /**
     * @param int $entity
     * @return bool|string
     */
    protected function getTableName($entity)
    {
        if (!isset($this->tableNames[$entity])) {
            try {
                $this->tableNames[$entity] = $this->resourceConnection->getTableName($entity);
            } catch (\Exception $e) {
                return false;
            }
        }
        return $this->tableNames[$entity];
    }

    /**
     * @param $dateTime
     * @return string
     */
    public function formatDate($dateTime)
    {
        $dateTimeAsTimeZone = $this->timezone
            ->date($dateTime)
            ->format('YmdHis');
        return $dateTimeAsTimeZone;
    }

    /**
     * @param array $reviews
     * @return array
     */
    public function getExportData($reviews)
    {
        $data[0] = ['Nick name', 'Date', 'Title', 'Details', 'Rating option',
            'Product SKU', 'Product ID', 'Status', 'Customer ID', 'Type', 'Store View Code', 'Review ID'];
        foreach ($reviews as $review) {
            $ratings = $this->getRatingOption($review['review_id']);
            $ratingOption = "";
            foreach ($ratings as $rating) {
                $ratingOption .= $rating['rating_code'] . ":" . $rating['value'] . ", ";
            }
            $ratingOption = substr($ratingOption, 0, strlen($ratingOption)-2);
            $row = null;
            $row[] = $review['nickname'];

            //format date
            $date = explode(" ", $review['created_at']);
            $time = $date[1];
            $date = explode("-", $date[0]);
            $interim = $date[0];
            $date[0] = $date[2];
            $date[2] = $interim;
            $row[] = $date[0] . "/" . $date[1] . "/" . $date[2] . " " . $time;

            $row[] = $review['title'];
            $row[] = $review['detail'];
            $row[] = $ratingOption;
            $row[] = $review['sku'];
            $row[] = $review['entity_pk_value'];

            $row[] = $review['status_code'];

            $row[] = $review['customer_id'];

            if ($review['store_id'] == 0) {
                $row[] = "admin";
            } elseif (($review['store_id'] != 0) && ($review['customer_id'] == null)) {
                $row[] = "guest";
            } elseif ($review['customer_id'] != null) {
                $row[] = "customer";
            } else {
                $row[] = "";
            }

            $storeViewCode = "";
            foreach ($this->getStoreViewCode($review['review_id']) as $storeCode) {
                $storeViewCode .= $storeCode['code'];
                $storeViewCode .= "|";
            }
            $row[] = rtrim($storeViewCode, "|");

            $row[] = $review['review_id'];
            $data[] = $row;
        }
        return $data;
    }
}
