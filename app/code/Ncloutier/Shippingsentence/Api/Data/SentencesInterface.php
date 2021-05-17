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

interface SentencesInterface
{

    const SENTENCES_ID = 'sentences_id';
    const SENTENCE = 'sentence';
    const MANUFACTURER = 'manufacturer';


    /**
     * Get sentences_id
     * @return string|null
     */
    public function getSentencesId();

    /**
     * Set sentences_id
     * @param string $sentencesId
     * @return \Ncloutier\Shippingsentence\Api\Data\SentencesInterface
     */
    public function setSentencesId($sentencesId);

    /**
     * Get sentence
     * @return string|null
     */
    public function getSentence();

    /**
     * Set sentence
     * @param string $sentence
     * @return \Ncloutier\Shippingsentence\Api\Data\SentencesInterface
     */
    public function setSentence($sentence);

    /**
     * Get manufacturer
     * @return string|null
     */
    public function getManufacturer();

    /**
     * Set manufacturer
     * @param string $manufacturer
     * @return \Ncloutier\Shippingsentence\Api\Data\SentencesInterface
     */
    public function setManufacturer($manufacturer);
}
