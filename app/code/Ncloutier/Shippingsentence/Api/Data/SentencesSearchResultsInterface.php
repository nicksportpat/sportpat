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

namespace Ncloutier\Shippingsentence\Api\Data;

interface SentencesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Sentences list.
     * @return \Ncloutier\Shippingsentence\Api\Data\SentencesInterface[]
     */
    public function getItems();

    /**
     * Set sentence list.
     * @param \Ncloutier\Shippingsentence\Api\Data\SentencesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
