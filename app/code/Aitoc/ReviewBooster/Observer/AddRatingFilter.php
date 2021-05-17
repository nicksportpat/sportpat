<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_ReviewBooster
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */
namespace Aitoc\ReviewBooster\Observer;

use Aitoc\ReviewBooster\Api\Data\ReviewDetailsInterface;
use Aitoc\ReviewBooster\Model\ResourceModel\ReviewDetails;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

class AddRatingFilter implements ObserverInterface
{
    const DATA_KEY_COLLECTION = 'collection';
    const REQUEST_PARAM_NAME_RATING = 'rating';
    const RATING_INTERVALS = [
        1 => [
            'min' => 1,
            'max' => 20
        ],
        2 => [
            'min' => 21,
            'max' => 40
        ],
        3 => [
            'min' => 41,
            'max' => 60
        ],
        4 => [
            'min' => 61,
            'max' => 80
        ],
        5 => [
            'min' => 81,
            'max' => 100
        ]
    ];

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Class constructor
     *
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Add rating filter
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var ReviewCollection $collection */
        $collection = $event->getData(self::DATA_KEY_COLLECTION);
        $select = $collection->getSelect();

        $extendedReviewInfoPrefixedTableName = $collection->getTable(ReviewDetails::TABLE);
        $ratinOptionVotePrefixedTableName = $collection->getTable('rating_option_vote');

        $select
            ->joinLeft(
                ['aitoc_extended' => $extendedReviewInfoPrefixedTableName],
                'aitoc_extended.review_id = main_table.review_id',
                [
                    ReviewDetailsInterface::PRODUCT_ADVANTAGES,
                    ReviewDetailsInterface::PRODUCT_DISADVANTAGES,
                    ReviewDetailsInterface::REVIEW_HELPFUL,
                    ReviewDetailsInterface::REVIEW_UNHELPFUL,
                    ReviewDetailsInterface::CUSTOMER_VERIFIED,
                    ReviewDetailsInterface::REVIEW_REPORTED,
                    ReviewDetailsInterface::COMMENT,
                    ReviewDetailsInterface::ADMIN_TITLE
                ]
            )->joinLeft(
                ['rating_vote' => $ratinOptionVotePrefixedTableName],
                'rating_vote.review_id = main_table.review_id',
                [
                    'group_concat(percent) as percent',
                    'count(*) as vote_count'
                ]
            );

        $select->group('main_table.review_id');

        $request = $this->request;
        $rating = $this->getRequestedRating($request);

        if ($rating) {
            $ratingInterval = $this->getRatingIntervals();

            $collection
                ->addFieldToFilter('percent', ['lteq' => $ratingInterval[$rating]['max']])
                ->addFieldToFilter('percent', ['gteq' => $ratingInterval[$rating]['min']]);
        }

        $select->order('ABS(IF(review_helpful IS NULL, 0, review_helpful) - IF(review_unhelpful IS NULL, 0, review_unhelpful)) DESC');
    }

    /**
     * @param RequestInterface $request
     * @return int|null
     */
    private function getRequestedRating(RequestInterface $request)
    {
        return $request->getParam(self::REQUEST_PARAM_NAME_RATING);
    }

    /**
     * Get rating equivalents of points and percents
     *
     * @return array
     */
    private function getRatingIntervals()
    {
        return self::RATING_INTERVALS;
    }
}
