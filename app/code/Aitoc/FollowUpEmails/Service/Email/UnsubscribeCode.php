<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2020 Aitoc (https://www.aitoc.com)
 * @package Aitoc_FollowUpEmailsEnt
 */

/**
 * Copyright © 2019 Aitoc. All rights reserved.
 */

namespace Aitoc\FollowUpEmails\Service\Email;

use Aitoc\FollowUpEmails\Api\Service\Email\UnsubscribeCodeInterface;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Class UnsubscribeCode
 */
class UnsubscribeCode implements UnsubscribeCodeInterface
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        EncryptorInterface $encryptor
    ) {
        $this->encryptor = $encryptor;
    }

    /**
     * @return string
     */
    public function generateUnsubscribeCode()
    {
        $string = $this->getUniqueString();

        return $this->getHash($string);
    }

    /**
     * @return string
     */
    private function getUniqueString()
    {
        return uniqid('', true);
    }

    /**
     * @param string $string
     * @return string
     */
    private function getHash($string)
    {
        return $this->encryptor->getHash($string);
    }

}