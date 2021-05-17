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

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Review
{
    const PUBLISHED_STATE = 1;

    const DEFAULT_SEPARATOR = "|";

    const VALID_COLUMN_NAMES = [
        'detail',
        'title',
        'nick_name',
        'type',
        'review_id',
        'customer_id',
        'status',
        'sku',
        'rating_option',
        'store_code',
        'date'
    ];

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $readAdapter;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $writeAdapter;

    /**
     * @var array
     */
    protected $tableNames = [];

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var int
     */
    protected $storeDefaultId;

    /**
     * @var int
     */
    protected $insertedRows = 0;

    /**
     * @var int
     */
    protected $existingRows = 0;

    /**
     * @var int
     */
    protected $invalidDataRows = 0;

    /**
     * @var string
     */
    protected $emptyRequiredDataRows = "";

    /**
     * @var string
     */
    protected $invalidDateRows = "";

    /**
     * @var string
     */
    protected $invalidStatusRows = "";

    /**
     * @var string
     */
    protected $invalidRatingRows = "";

    /**
     * @var string
     */
    protected $invalidReviewIdRows = "";

    /**
     * @var string
     */
    protected $invalidProductSkuRows = "";

    /**
     * @var int
     */
    protected $currentProductId;

    /**
     * @var array
     */
    protected $rating;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Bss\ReviewsImport\Model\Import\ReviewsImport
     */
    protected $reviewsImport;

    /**
     * Review constructor.
     * @param ResourceConnection $resourceConnection
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ReviewsImport $reviewsImport
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\Product $product,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Bss\ReviewsImport\Model\ResourceModel\ReviewsImport $reviewsImport
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->product = $product;
        $this->storeManager = $storeManager;
        $this->reviewsImport = $reviewsImport;

        $this->readAdapter = $this->resourceConnection->getConnection('core_read');
        $this->writeAdapter = $this->resourceConnection->getConnection('core_write');
    }

    /**
     * @return void
     */
    public function init()
    {
        $this->storeDefaultId = $this->storeManager->getDefaultStoreView()->getId();
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
     * @return array|bool
     */
    protected function getRatingTable()
    {
        if (!isset($this->rating)) {
            try {
                $select = $this->readAdapter->select()
                    ->from(
                        $this->getTableName('rating'),
                        ['rating_id', 'rating_code']
                    );
                $ratings = $this->readAdapter->query($select);
                foreach ($ratings as $rating) {
                    $this->rating[] = $rating;
                }
            } catch (\Exception $e) {
                return false;
            }
        }
        return $this->rating;
    }

    protected function getLastestRating()
    {
        $select = $this->readAdapter->select()
            ->from(
                $this->getTableName('rating'),
                ['rating_id', 'rating_code']
            )->order('rating_id DESC')->limit(1);
        $rating = $this->readAdapter->fetchRow($select);
        return $rating;
    }

    /**
     * @param $data
     * @return array
     */
    protected function getTrimedData($data) {
        $trimedData = [];
        foreach ($data as $key => $value) {
            $trimedData[$key] = trim($value);
        }
        return $trimedData;
    }

    /**
     * @param array $data
     * @param int $rowNum
     *
     * @return void
     */
    public function processData($data, $rowNum)
    {
        $reviewData = $this->prepareData($this->getTrimedData($data));

        $storeCodes = explode(self::DEFAULT_SEPARATOR, $reviewData['store_code']);
        foreach ($storeCodes as $storeCode) {
            $select = $this->readAdapter->select()
                ->from(
                    $this->getTableName('store'),
                    [
                        'store_id'
                    ]
                )->where('code = :store_code');
            $bind = [
                ':store_code' => $storeCode
            ];
            $importStoreId = $this->readAdapter->fetchOne($select, $bind);
            if ($importStoreId === false) {
                $storeId = $this->storeDefaultId;
            } else {
                $storeId = $importStoreId;
            }

            if ($this->validate($reviewData, $storeId, $rowNum)) {
                $inserted = false;
                $productId = $this->currentProductId;

                if ($productId > 0) {
                    $inserted = true;
                    $this->insertReview($productId, $storeId, $reviewData);
                }
                if ($inserted) {
                    $this->insertedRows++;
                }
            }
            break;
        }
    }

    /**
     * @param $data
     * @param $rowNum
     */
    public function validateBeforeImport($data, $rowNum)
    {
        $reviewData = $this->prepareData($data);

        $storeCodes = explode(self::DEFAULT_SEPARATOR, $reviewData['store_code']);
        foreach ($storeCodes as $storeCode) {
            $select = $this->readAdapter->select()
                ->from(
                    $this->getTableName('store'),
                    [
                        'store_id'
                    ]
                )->where('code = :store_code');
            $bind = [
                ':store_code' => $storeCode
            ];
            $importStoreId = $this->readAdapter->fetchOne($select, $bind);
            if ($importStoreId === false) {
                $storeId = $this->storeDefaultId;
            } else {
                $storeId = $importStoreId;
            }
            $this->validate($reviewData, $storeId, $rowNum);
            break;
        }
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        $data = $this->formatColumnsName($data);

        //prepare date
        $date = isset($data['date'])? $data['date']: '';

        $check = strpos($date, ' ');

        $data = $this->reviewsImport->setDateTime($date, $check, $data);

        //prepare rating

        if (isset($data['rating_option'])) {
            $stars = $this->getRatingStarByString($data['rating_option']);
            $ratingId = $this->getRatingId($this->getRatingTable(), $data['store_code'], $data['rating_option']);
            $ratingCode = $this->getRatingTypeByString($data['rating_option']);
            $data['stars'] = $stars;
            $data['rating_id'] = $ratingId;
            $data['rating_code'] = $ratingCode;
        }

        $checkCustomerId = false;

        if ($data['type'] == 'customer') {
            $select = $this->readAdapter->select()
                ->from(
                    $this->getTableName('customer_entity'),
                    [
                        'entity_id'
                    ]
                )->where('entity_id = :entity_id');
            $bind = [
                ':entity_id' => $data['customer_id']
            ];
            $checkCustomerId = $this->readAdapter->fetchOne($select, $bind);
        }

        if ($checkCustomerId == false) {
            $data['customer_id'] = null;
        }

        //prepare comment
        $data['comment'] = $this->getReviewComment($data);

        //prepare status
        $status = $this->reviewsImport->getStatusId($data['status']);
        $data['status'] = $status;

        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    public function formatColumnsName($data)
    {
        $columnNameKeys = [
            'details',
            'title',
            'nick',
            'type',
            'review',
            'customer',
            'status',
            'sku',
            'rating',
            'store',
            'date'
        ];
        foreach ($data as $key => $value) {
            $key_f = strtolower($key);
            foreach ($columnNameKeys as $index => $columnKey) {
                if (strpos($key_f, $columnKey) !== false) {
                    $data[self::VALID_COLUMN_NAMES[$index]] = $value;
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @param array $data
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function insertReview($productId, $storeId, $data)
    {
        $productReviewEntityId = $this->reviewsImport->getProductReviewEntityId();
        $checkReviewId = $this->reviewsImport->checkReviewId($data);
        //review table
        $saveReviewData = [
            'created_at' => $data['date'],
            'entity_id' => $productReviewEntityId,
            'entity_pk_value' => $productId,
            'status_id' => $data['status']
        ];
        $reviewData=[
            'review_id' => $data['review_id'],
            'created_at' => $data['date'],
            'entity_id' => $productReviewEntityId,
            'entity_pk_value' => $productId,
            'status_id' => $data['status']
        ];

        $this->reviewsImport->insertIntoTable($checkReviewId, "review", $data, $reviewData, $saveReviewData);

        $reviewId = empty($data['review_id'])?$this->reviewsImport->getLastReviewId():$data['review_id'];

        // review_deail table
        $reviewDetailStoreId = ($data['type'] == 'administrator') ? 0 : $storeId;
        $reviewDetailData = [
            'review_id' => $reviewId,
            'store_id' => $reviewDetailStoreId,
            'title' => $data['title'],
            'detail' => $data['comment'],
            'nickname' => $data['nick_name'],
            'customer_id' => $data['customer_id']
        ];

        $saveReviewDetailData=[
            'store_id' => $reviewDetailStoreId,
            'title' => $data['title'],
            'detail' => $data['comment'],
            'nickname' => $data['nick_name'],
            'customer_id' => $data['customer_id']
        ];

        $this->reviewsImport->insertIntoTable(
            $checkReviewId,
            "review_detail",
            $data,
            $reviewDetailData,
            $saveReviewDetailData
        );

        //review_store table
        $this->reviewsImport->insertReviewStore($data, $reviewId);

        for ($i=0; $i<count($data['stars']); $i++) {
            // get rating_option
            $select = $this->readAdapter->select()
                ->from(
                    $this->getTableName('rating_option'),
                    [
                        'option_id',
                        'rating_id',
                        'code',
                        'value'
                    ]
                )
                ->where('rating_id = :rating_id');
            $bind = [
                ':rating_id' => $data['rating_id'][$i]
            ];

            $options = $this->readAdapter->query($select, $bind);

            // rating
            $this->reviewsImport->insertRatingOptionVote($options, $i, $data, $reviewId, $productId);

            $select = $this->readAdapter->select()->from(
                $this->getTableName('rating_option_vote_aggregated'),
                ['store_id', 'primary_id']
            )->where(
                'rating_id = :rating_id'
            )->where(
                'entity_pk_value = :pk_value'
            );
            $bind = [':rating_id' => $data['rating_id'][$i], ':pk_value' => $productId];
            $oldData = $this->readAdapter->fetchPairs($select, $bind);

            $perStoreInfo = $this->reviewsImport->getStoreInfo($bind);

            $usedStores = [];
            foreach ($perStoreInfo as $row) {
                $saveData = [
                    'rating_id' => $data['rating_id'][$i],
                    'entity_pk_value' => $productId,
                    'vote_count' => $row['vote_count'],
                    'vote_value_sum' => $row['vote_value_sum'],
                    'percent' => $row['vote_value_sum'] / $row['vote_count'] / 5 * 100,
                    'percent_approved' => $row['app_vote_count']
                        ? $row['app_vote_value_sum'] / $row['app_vote_count'] / 5 * 100 : 0,
                    'store_id' => $row['store_id'],
                ];

                $this->reviewsImport->insertRatingOption($oldData, $row, $saveData);

                $usedStores[] = $row['store_id'];
            }

            $toDelete = array_diff(array_keys($oldData), $usedStores);

            $this->reviewsImport->deleteRatingOption($toDelete, $oldData);
        }

        $select = $this->readAdapter->select()->from(
            ['rating_vote' => $this->getTableName('rating_option_vote')],
            [
                'entity_pk_value' => 'rating_vote.entity_pk_value',
                'sum' => 'SUM(rating_vote.percent)', 'count' => 'COUNT(*)'
            ]
        )->join(
            ['review' => $this->getTableName('review')],
            'rating_vote.review_id=review.review_id',
            []
        )->join(
            ['rating_store' => $this->getTableName('rating_store')],
            'rating_vote.rating_id = rating_store.rating_id',
            []
        )->joinLeft(
            ['review_store' => $this->getTableName('review_store')],
            'rating_vote.review_id=review_store.review_id',
            ['review_store.store_id']
        );

        $select->join(
            ['review_status' => $this->getTableName('review_status')],
            'review.status_id = review_status.status_id',
            []
        )->where(
            'review_status.status_code = :status_code'
        )->where(
            'rating_store.store_id = :store_id'
        )->group(
            'rating_vote.entity_pk_value'
        )->group(
            'review_store.store_id'
        );
        $bind = [':status_code' => 'Approved'];

        $select->where('rating_vote.entity_pk_value = :pk_value');
        $bind[':pk_value'] = $productId;
        $bind[':store_id'] = $storeId;


        $aggregates = $this->readAdapter->query($select, $bind);

        $countAggr = 0;
        foreach ($aggregates as $agg) {
            $countAggr ++;
            if ($agg['store_id'] == $storeId) {
                $ratingSummary = round($agg['sum']/$agg['count']);
            }
        }

        if ($countAggr) {
            // get total reviews
            $reviewsCount = $this->reviewsImport->getTotalReviews($productId, $storeId);

            $reviewSummaryData = [
                'entity_pk_value' => $productId,
                'entity_type' => $productReviewEntityId,
                'reviews_count' => $reviewsCount,
                'rating_summary' => $ratingSummary,
                'store_id' => $storeId
            ];

            $select = $this->readAdapter->select()->from($this->getTableName('review_entity_summary'))
                ->where('entity_pk_value = :pk_value')
                ->where('entity_type = :entity_type')
                ->where('store_id = :store_id');
            $bind = [
                ':pk_value' => $productId,
                ':entity_type' => $productReviewEntityId,
                ':store_id' => $storeId,
            ];
            $oldData = $this->readAdapter->fetchRow($select, $bind);
            $this->reviewsImport->insertReviewEntitySumary($oldData, $reviewSummaryData);
        }
    }

    /**
     * @param null $importRatingOption
     * @return array
     */
    protected function getRatingStarByString($importRatingOption = null)
    {
        $stars = null;
        $ratingOption=explode(',', $importRatingOption);
        for ($i=0; $i<count($ratingOption); $i++) {
            $ratingArr=explode(':', $ratingOption[$i]);
            if (isset($ratingArr[1])) {
                $stars[$i] = trim($ratingArr[1]);
            }
        }
        return $stars;
    }

    /**
     * @param null $importRatingOption
     * @return string
     */
    protected function getRatingTypeByString($importRatingOption = null)
    {
        $ratingOption=explode(',', $importRatingOption);
        for ($i=0; $i<count($ratingOption); $i++) {
            $ratingArr=explode(':', $ratingOption[$i]);
            $ratingType[$i]=trim($ratingArr[0]);
        }
        return $ratingType;
    }

    /**
     * @param $ratings
     * @param $storeCode
     * @param null $importRatingOption
     * @return mixed
     */
    protected function getRatingId($ratings, $storeCode, $importRatingOption = null)
    {
        $ratingCode = $this->getRatingTypeByString($importRatingOption);
        for ($i=0; $i<count($ratingCode); $i++) {
            $check=false;
            foreach ($ratings as $rating) {
                if ($rating['rating_code'] == $ratingCode[$i]) {
                    $id = $rating['rating_id'];
                    $check=true;
                    break;
                }
            }
            if (!$check) {
                $ratingData=[
                    'rating_code' => $ratingCode[$i],
                    'entity_id' => 1,
                    'position' => 0,
                    'is_active' => 1
                ];
                $this->writeAdapter->insert($this->getTableName('rating'), $ratingData);
                $lastRating = $this->getLastestRating();
                $this->rating[] = $lastRating;

                $stores = $this->reviewsImport->getImportStoreIds($storeCode);

                foreach ($stores as $store) {
                    $ratingStoreData = [
                        'rating_id' => $lastRating['rating_id'],
                        'store_id' => $store
                    ];

                    $this->writeAdapter->insert($this->getTableName('rating_store'), $ratingStoreData);

                }

                $select = $this->readAdapter->select()
                    ->from(
                        $this->getTableName('rating'),
                        ['rating_id']
                    )
                    ->where('rating_code = :rating_code');
                $bind = [
                    ':rating_code' => $ratingCode[$i],
                ];
                $id = $this->readAdapter->fetchOne($select, $bind);

                for ($value=1; $value<=5; $value++) {
                    $ratingOptionData = [
                        'rating_id' => $id,
                        'code' => $value,
                        'value' => $value,
                        'position' => $value
                    ];
                    $this->writeAdapter->insert($this->getTableName('rating_option'), $ratingOptionData);
                }
            }
            $ratingId[$i] = $id;
        }
        return $ratingId;
    }

    /**
     * @param array $data
     * @return string
     */
    protected function getReviewComment($data)
    {
        $productCmt = $data['detail'];
        $productCmt = trim($productCmt);
        $productCmt = trim($productCmt, '"');

        return $productCmt;
    }

    /**
     * @param array $reviewData
     * @param int $storeId
     * @param int $rowNum
     * @return bool
     */
    protected function validate($reviewData, $storeId, $rowNum)
    {
        $reviewId = (int) $reviewData['review_id'];
        if (!$this->validateDate($reviewData, $rowNum) ||
            !$this->validateReviewId($reviewData, $reviewId, $rowNum) ||
            !$this->validateRating($reviewData, $rowNum) ||
            !$this->validateProductSku($reviewData, $rowNum) ||
            !$this->validateRequiredValue($reviewData, $rowNum)
        ) {
            $this->invalidDataRows++;
            return false;
        }

        $productSku = $reviewData['sku'];

        $productId = $this->product->getIdBySku($productSku);

        $resultDefault = $this->checkExistingReview($productId, $storeId, $reviewData);

        if (!$resultDefault) {
            $this->existingRows++;
            return false;
        }

        return true;
    }

    /**
     * @param array $reviewData
     * @param int $rowNum
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function validateRequiredValue($reviewData, $rowNum)
    {
        if (!$reviewData['nick_name'] ||
            $reviewData['nick_name'] == '' ||
            !$reviewData['title'] ||
            $reviewData['title'] == '' ||
            !$reviewData['date'] ||
            $reviewData['date'] == '' ||
            !$reviewData['comment'] ||
            $reviewData['comment'] == '' ||
            $reviewData['status'] == 0) {
            $this->emptyRequiredDataRows .= "$rowNum, ";
            return false;
        }
        return true;
    }

    /**
     * @param array $reviewData
     * @param int $reviewId
     * @param int $rowNum
     * @return bool
     */
    protected function validateReviewId($reviewData, $reviewId, $rowNum)
    {
        if (($reviewId < 1) && (!empty($reviewData['review_id']))) {
            $this->invalidReviewIdRows .= "$rowNum, ";
            return false;
        }
        return true;
    }

    /**
     * @param array $reviewData
     * @param int $rowNum
     * @return bool
     */
    protected function validateRating($reviewData, $rowNum)
    {
        $patternRating = '/([a-z]|[A-Z])+:[1-5]{1}/';

        if (empty($reviewData['rating_option']) || !preg_match($patternRating, $reviewData['rating_option'])) {
            $this->invalidRatingRows .= "$rowNum, ";
            return false;
        }

        $stars = [1,2,3,4,5];
        $starsLeng = count($stars);
        if (isset($reviewData['stars'])) {
            for ($i = 0; $i < count($reviewData['stars']); $i++) {
                $check=0;
                for ($j = 0; $j < $starsLeng; $j++) {
                    $star = (string) $stars[$j];
                    if ($reviewData['stars'][$i] === $star) {
                        $check++;
                    }
                }
                if ($check == 0) {
                    $this->invalidRatingRows .= "$rowNum, ";
                    return false;
                }
            }
        } else {
            $this->invalidRatingRows .= "$rowNum, ";
            return false;
        }

        return true;
    }

    /**
     * @param array $reviewData
     * @param int $rowNum
     * @return bool
     */
    protected function validateProductSku($reviewData, $rowNum)
    {
        $productSku = $reviewData['sku'];

        $productId = $this->product->getIdBySku($productSku);

        if (!($productId > 0)) {
            $this->invalidProductSkuRows .= "$rowNum, ";
            return false;
        }

        return true;
    }

    /**
     * @param array $reviewData
     * @param int $rowNum
     * @return bool
     */
    protected function validateDate($reviewData, $rowNum)
    {
        $patternDate = '/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/';

        if (!preg_match($patternDate, $reviewData['date'])) {
            $this->invalidDateRows .= "$rowNum, ";
            return false;
        }

        return true;
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @param array $reviewData
     * @return bool
     */
    protected function checkExistingReview($productId, $storeId, $reviewData)
    {
        if (!($productId > 0)) {
            return false;
        }

        $select = $this->readAdapter->select()
            ->from(
                ['main_table' => $this->getTableName('review')],
                [
                    'review_id',
                    'entity_pk_value'
                ]
            )
            ->join(
                ['rd' => $this->getTableName('review_detail')],
                'main_table.review_id = rd.review_id',
                ['store_id', 'title', 'detail']
            )
            ->where('main_table.entity_pk_value = :pk_value')
            ->where('rd.store_id = :store_id')
            ->where('rd.detail = :detail')
            ->where('rd.nickname = :nickname')
            ->where('rd.title = :title');
        $bind = [
            ':pk_value' => $productId,
            ':store_id' => $storeId,
            ':detail' => $reviewData['comment'],
            ':nickname' => $reviewData['nick_name'],
            ':title' => $reviewData['title'],
        ];
        $review = $this->readAdapter->fetchOne($select, $bind);

        if ($review &&
            empty($reviewData['review_id'])) {
            return false;
        }

        $this->currentProductId = $productId;

        return true;
    }

    /**
     * @return int
     */
    public function getInsertedRows()
    {
        return $this->insertedRows;
    }

    /**
     * @return int
     */
    public function getExistingRows()
    {
        return $this->existingRows;
    }

    /**
     * @return int
     */
    public function getInvalidRows()
    {
        return $this->invalidDataRows;
    }

    /**
     * @param null $code
     * @return int|string
     */
    public function getErrorRows($code = null)
    {
        switch ($code) {
            case "emptyRequiredDataRows":
                return $this->emptyRequiredDataRows;
                break;
            case "invalidDateRows":
                return $this->invalidDateRows;
                break;
            case "invalidStatusRows":
                return $this->invalidStatusRows;
                break;
            case "invalidRatingRows":
                return $this->invalidRatingRows;
                break;
            case "invalidReviewIdRows":
                return $this->invalidReviewIdRows;
                break;
            case "invalidProductSkuRows":
                return $this->invalidProductSkuRows;
                break;
            default:
                return $this->invalidDataRows;
        }
    }
}
