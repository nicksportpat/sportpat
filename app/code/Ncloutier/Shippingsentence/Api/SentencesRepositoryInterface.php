<?php
/**
 * A Magento 2 module named Ncloutier/Shippingsentence
 * Copyright (C) 2017  
 * 
 * This file is part of Ncloutier/Shippingsentence.
 * 
 * Ncloutier/Shippingsentence is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Ncloutier\Shippingsentence\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SentencesRepositoryInterface
{


    /**
     * Save Sentences
     * @param \Ncloutier\Shippingsentence\Api\Data\SentencesInterface $sentences
     * @return \Ncloutier\Shippingsentence\Api\Data\SentencesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Ncloutier\Shippingsentence\Api\Data\SentencesInterface $sentences
    );

    /**
     * Retrieve Sentences
     * @param string $sentencesId
     * @return \Ncloutier\Shippingsentence\Api\Data\SentencesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($sentencesId);

    /**
     * Retrieve Sentences matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Ncloutier\Shippingsentence\Api\Data\SentencesSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Sentences
     * @param \Ncloutier\Shippingsentence\Api\Data\SentencesInterface $sentences
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Ncloutier\Shippingsentence\Api\Data\SentencesInterface $sentences
    );

    /**
     * Delete Sentences by ID
     * @param string $sentencesId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($sentencesId);
}
