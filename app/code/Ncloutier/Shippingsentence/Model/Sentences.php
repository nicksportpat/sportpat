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

namespace Ncloutier\Shippingsentence\Model;

use Ncloutier\Shippingsentence\Api\Data\SentencesInterface;

class Sentences extends \Magento\Framework\Model\AbstractModel implements SentencesInterface
{

    protected $_eventPrefix = 'ncloutier_shippingsentence_sentences';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ncloutier\Shippingsentence\Model\ResourceModel\Sentences');
    }

    /**
     * Get sentences_id
     * @return string
     */
    public function getSentencesId()
    {
        return $this->getData(self::SENTENCES_ID);
    }

    /**
     * Set sentences_id
     * @param string $sentencesId
     * @return \Ncloutier\Shippingsentence\Api\Data\SentencesInterface
     */
    public function setSentencesId($sentencesId)
    {
        return $this->setData(self::SENTENCES_ID, $sentencesId);
    }

    /**
     * Get sentence
     * @return string
     */
    public function getSentence()
    {
        return $this->getData(self::SENTENCE);
    }

    /**
     * Set sentence
     * @param string $sentence
     * @return \Ncloutier\Shippingsentence\Api\Data\SentencesInterface
     */
    public function setSentence($sentence)
    {
        return $this->setData(self::SENTENCE, $sentence);
    }

    /**
     * Get manufacturer
     * @return string
     */
    public function getManufacturer()
    {
        return $this->getData(self::MANUFACTURER);
    }

    /**
     * Set manufacturer
     * @param string $manufacturer
     * @return \Ncloutier\Shippingsentence\Api\Data\SentencesInterface
     */
    public function setManufacturer($manufacturer)
    {
        return $this->setData(self::MANUFACTURER, $manufacturer);
    }
}
