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

class ReviewsImport
{
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
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var array
     */
    protected $ratingOptions;

    /**
     * @var array
     */
    protected $status;

    /**
     * @var array
     */
    protected $tableNames = [];

    /**
     * @param ResourceConnection $resourceConnection
     * @param \Magento\Catalog\Model\Product $product
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\Product $product
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->product = $product;

        $this->readAdapter = $this->resourceConnection->getConnection('core_read');
        $this->writeAdapter = $this->resourceConnection->getConnection('core_write');
    }

    /**
     * @param int $entity
     * @return bool|string
     */
    public function getTableName($entity)
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
     * @param array $toDelete
     * @param array $oldData
     */
    public function deleteRatingOption($toDelete, $oldData)
    {
        foreach ($toDelete as $storeId) {
                $condition = "primary_id = $oldData[$storeId]";
                $select = $this->readAdapter->select()
                    ->from($this->getTableName('rating_option_vote_aggregated'))
                    ->where($condition);
                $this->writeAdapter->deleteFromSelect($select, $this->getTableName('rating_option_vote_aggregated'));
        }
    }

    /**
     * @param array $oldData
     * @param array $row
     * @param array $saveData
     */
    public function insertRatingOption($oldData, $row, $saveData)
    {
        if (isset($oldData[$row['store_id']])) {
            $condition = ['primary_id = ?' => $oldData[$row['store_id']]];
            $this->writeAdapter
                ->update($this->getTableName('rating_option_vote_aggregated'), $saveData, $condition);
        } else {
            $this->writeAdapter->insert($this->getTableName('rating_option_vote_aggregated'), $saveData);
        }
    }

    /**
     * @param array $data
     * @param int $reviewId
     */
    public function insertReviewStore($data, $reviewId)
    {
        $stores = $this->getImportStoreIds($data['store_code']);

        foreach ($stores as $store) {
            $reviewStoreData = [
                'review_id' => $reviewId,
                'store_id' => $store
            ];
            if (empty($this->checkExistReviewStoreId($reviewId, $store))) {
                $this->writeAdapter->insert($this->getTableName('review_store'), $reviewStoreData);
            }
        }
    }

    /**
     * @param $bind
     * @return \Zend_Db_Statement_Interface
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getStoreInfo($bind)
    {
        $appVoteCountCond = $this->readAdapter->getCheckSql('review.status_id=1', 'vote.vote_id', 'NULL');
        $appVoteValueSumCond = $this->readAdapter->getCheckSql('review.status_id=1', 'vote.value', '0');

        //phpcs:disable
            $select = $this->readAdapter->select()->from(
                ['vote' => $this->getTableName('rating_option_vote')],
                [
                    'vote_count' => 'COUNT(vote.vote_id)',
                    'vote_value_sum' => 'SUM(vote.value)',
                    'app_vote_count' => "COUNT({$appVoteCountCond})",
                    'app_vote_value_sum' => "SUM({$appVoteValueSumCond})"
                ]
            )->join(
                ['review' => $this->getTableName('review')],
                'vote.review_id=review.review_id',
                []
            )->joinLeft(
                ['store' => $this->getTableName('review_store')],
                'vote.review_id=store.review_id',
                'store_id'
            )->join(
                ['rstore' => $this->getTableName('rating_store')],
                'vote.rating_id=rstore.rating_id AND rstore.store_id=store.store_id',
                []
            )->where(
                'vote.rating_id = :rating_id'
            )->where(
                'vote.entity_pk_value = :pk_value'
            )->group(
                ['vote.rating_id', 'vote.entity_pk_value', 'store.store_id']
            );
            //phpcs:enable
            return $this->readAdapter->query($select, $bind);
    }

    /**
     * @param array $oldData
     * @param array $reviewSummaryData
     */
    public function insertReviewEntitySumary($oldData, $reviewSummaryData)
    {
        if (isset($oldData['primary_id']) && $oldData['primary_id'] > 0) {
            $condition = ["{$this->getTableName('review_entity_summary')}.primary_id = ?" => $oldData['primary_id']];
            $this->writeAdapter->update($this->getTableName('review_entity_summary'), $reviewSummaryData, $condition);
        } else {
            $this->writeAdapter->insert($this->getTableName('review_entity_summary'), $reviewSummaryData);
        }
    }

    /**
     * @return int
     */
    public function getProductReviewEntityId()
    {
        $select = $this->readAdapter->select()
            ->from(
                [$this->getTableName('review_entity')],
                ['entity_id']
            )
            ->where('entity_code = :entity_code');
        $bind = [
            ':entity_code' => 'product',
        ];
        $entityId = $this->readAdapter->fetchOne($select, $bind);
        return $entityId;
    }

    /**
     * @param array $data
     * @return string
     */
    public function checkReviewId($data)
    {
        $select = $this->readAdapter->select()
            ->from(
                $this->getTableName('review'),
                [
                    'review_id'
                ]
            )
            ->where('review_id = :review_id');
        $bind = [
            ':review_id' => $data['review_id'],
        ];
        $reviewId = $this->readAdapter->fetchOne($select, $bind);
        return $reviewId;
    }

    /**
     * @param int $checkReviewId
     * @param string $tableName
     * @param array $data
     * @param array $reviewData
     * @param array $saveData
     * @return void
     */
    public function insertIntoTable($checkReviewId, $tableName, $data, $reviewData, $saveData)
    {
        if ($checkReviewId>0) {
            $condition = ["{$this->getTableName($tableName)}.review_id = ?" => $data['review_id']];
            $this->writeAdapter->update($this->getTableName($tableName), $saveData, $condition);
        } else {
            $this->writeAdapter->insert($this->getTableName($tableName), $reviewData);
        }
    }

    /**
     * @return int
     */
    public function getLastReviewId()
    {
        //get last review id
        $select = $this->readAdapter->select()
            ->from(
                [$this->getTableName('review')],
                ['review_id']
            )
            ->order('review_id DESC')
            ->limit(1);
        $maxId = $this->readAdapter->fetchOne($select);
        return $maxId;
    }

    /**
     * @param array $storeCodes
     * @return array
     */
    public function getImportStoreIds($storeCodes)
    {
        $storeIds[] = 0;
        $storeCodes = explode(Review::DEFAULT_SEPARATOR, $storeCodes);
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
            if ($importStoreId > 0) {
                $storeIds[] = $importStoreId;
            }

        }
        return $storeIds;
    }

    /**
     * @param int $reviewId
     * @param int $storeId
     * @return string
     */
    public function checkExistReviewStoreId($reviewId, $storeId)
    {
        $select = $this->readAdapter->select()
            ->from(
                $this->getTableName('review_store'),
                [
                    'review_id'
                ]
            )
            ->where('review_id = :review_id')
            ->where('store_id = :store_id');
        $bind = [
            ':review_id' => $reviewId,
            ':store_id' => $storeId
        ];
        $reviewStoreId = $this->readAdapter->fetchOne($select, $bind);
        return $reviewStoreId;
    }

    /**
     * @param int $i
     * @param array $data
     * @param array $saveData
     * @param array $reviewData
     * @return void
     */
    public function insertRatingOptionVote($options, $i, $data, $reviewId, $productId)
    {
        foreach ($options as $option) {
            $this->ratingOptions[$option['value']] = $option['option_id'];
        }
        $percent = $data['stars'][$i] * 20;
        $newReviewId = $data['review_id']==""?$reviewId:$data['review_id'];
        $reviewData = [
            'option_id' => $this->ratingOptions[$data['stars'][$i]],
            'remote_ip' => '::1',
            'remote_ip_long' => 0,
            'entity_pk_value' => $productId,
            'rating_id' => $data['rating_id'][$i],
            'review_id' => $newReviewId,
            'percent' => $percent,
            'value' => $data['stars'][$i]
        ];

        $saveData = [
            'option_id' => $this->ratingOptions[$data['stars'][$i]],
            'remote_ip' => '::1',
            'remote_ip_long' => 0,
            'entity_pk_value' => $productId,
            'rating_id' => $data['rating_id'][$i],
            'percent' => $percent,
            'value' => $data['stars'][$i]
        ];
        $ratingIds = $data['rating_id'];
        $select = $this->readAdapter->select()
            ->from(
                $this->getTableName('rating_option_vote'),
                [
                    'vote_id'
                ]
            )->where('review_id = :review_id')
            ->where('rating_id = :rating_id');
        $bind = [
            ':review_id' => $data['review_id'],
            ':rating_id' => $ratingIds[$i]
        ];
        $voteId = $this->readAdapter->fetchOne($select, $bind);
        if ($voteId>0) {
            $condition = ["{$this->getTableName('rating_option_vote')}.vote_id = ?" => $voteId];
            $this->writeAdapter->update($this->getTableName('rating_option_vote'), $saveData, $condition);
        } else {
            $this->writeAdapter->insert($this->getTableName('rating_option_vote'), $reviewData);
        }
    }

    /**
     * @param int $productId
     * @param int $storeId
     * @return string
     */
    public function getTotalReviews($productId, $storeId)
    {
        $select = $this->readAdapter->select()->from(
            ['main_table' => $this->getTableName('review')],
            ['review_count' => 'COUNT(*)']
        )->where(
            "main_table.entity_pk_value = :pk_value"
        );
        $bind = [':pk_value' => $productId];
        if ($storeId > 0) {
            $select->join(
                ['store' => $this->getTableName('review_store')],
                'main_table.review_id=store.review_id AND store.store_id = :store_id',
                []
            );
            $bind[':store_id'] = (int) $storeId;
        }

        $select->where("main_table.status_id = :status_id");
        $bind[':status_id'] = Review::PUBLISHED_STATE;

        return $this->readAdapter->fetchOne($select, $bind);
    }

    /**
     * @param null $statusCode
     * @return int
     */
    public function getStatusId($statusCode = null)
    {
        $notApproved=0;
        if (empty($statusCode)) {
            $statusId=0;
        } else {
            $statuses = $this->getStatusTable();
            foreach ($statuses as $status) {
                if ($statusCode == $status['status_code']) {
                    $statusId = $status['status_id'];
                }
                if ($status['status_code']=="Not Approved") {
                    $notApproved = $status['status_id'];
                }
            }
        }
        if (!isset($statusId)) {
            $statusId = $notApproved;
        }
        return $statusId;
    }

    /**
     * @return array|bool
     */
    public function getStatusTable()
    {
        if (!isset($this->status)) {
            try {
                $select = $this->readAdapter->select()
                    ->from(
                        $this->getTableName('review_status'),
                        ['status_id', 'status_code']
                    );
                $statuses = $this->readAdapter->query($select);
                foreach ($statuses as $status) {
                    $this->status[] = $status;
                }
            } catch (\Exception $e) {
                return false;
            }
        }
        return $this->status;
    }

    /**
     * @param $date
     * @param $check
     * @param $data
     * @return mixed
     */
    public function setDateTime($date, $check, $data)
    {
        if ($date && $date != '' && $check > 0) {
            $date = explode(' ', $date);
            $time = array_merge(explode(':', $date[1]), ["00", "00", "00"]);

            $date = explode('/', $date[0]);
            $newDate = '';
            if (isset($date[2])) {
                $newDate = sprintf("%04d", $date[2])
                . '-' . sprintf("%02d", $date[1])
                . '-' . sprintf("%02d", $date[0])
                . ' ' . sprintf("%02d", $time[0])
                . ':' . sprintf("%02d", $time[1])
                . ':' . sprintf("%02d", $time[2]);
            }
            $data['date'] = $newDate;
        } else {
            $data['date'] = "";
        }
        return $data;
    }
}
