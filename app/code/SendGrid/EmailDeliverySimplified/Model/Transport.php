<?php

namespace SendGrid\EmailDeliverySimplified\Model;

use \Psr\Log\LoggerInterface;
use \Magento\Framework\Phrase;
use \Magento\Framework\Mail\MessageInterface;
use \Magento\Framework\Exception\MailException;
use \Magento\Framework\Mail\TransportInterface;
use \Magento\Framework\Module\Manager;
use SendGrid\EmailDeliverySimplified\Helper\API;
use SendGrid\EmailDeliverySimplified\Helper\Tools;
use SendGrid\EmailDeliverySimplified\Model\GeneralSettings;

class Transport extends \Zend_Mail_Transport_Smtp implements TransportInterface
{
  /**
   * @var \Magento\Framework\Mail\MessageInterface
   */
    protected $_message;

  /**
   * @var \Psr\Log\LoggerInterface
   */
    protected $_logger;

  /**
   * @var SendGrid\EmailDeliverySimplified\Model\GeneralSettings
   */
    protected $_generalSettings;

  /**
   * @var \Magento\Framework\Module\Manager
   */
    protected $_moduleManager;

  /**
   * @const   string  SendGrid SMTP hostname
   */
    const SMTP_HOSTNAME = 'smtp.sendgrid.net';

  /**
   * @param   MessageInterface  $message
   * @param   GeneralSettings   $generalSettings
   * @param   LoggerInterface   $loggerInterface
   * @throws  \InvalidArgumentException
   */
    public function __construct(
        MessageInterface $message,
        GeneralSettings $generalSettings,
        LoggerInterface $loggerInterface,
        Manager         $moduleManager
    ) {
        if (! $message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }

        $this->_logger          = $loggerInterface;
        $this->_message         = $message;
        $this->_generalSettings = $generalSettings;
        $this->_moduleManager   = $moduleManager;
   
        $smtp_port = $this->_generalSettings->getSMTPPort();
        if (empty($smtp_port)) {
            $smtp_port = 587;
        }

        $apikey = $this->_generalSettings->getAPIKey();
        if (empty($apikey) || ! $this->_moduleManager->isOutputEnabled('SendGrid_EmailDeliverySimplified')) {
            parent::__construct();

            return;
        }

        $smtp_conf = [
        'auth'     => 'login',
        'ssl'      => 'tls',
        'port'     => $smtp_port,
        'username' => 'apikey',
        'password' => $apikey
        ];
    
        $this->_updateInternalMessage();
        $this->_sent = false;

        parent::__construct(self::SMTP_HOSTNAME, $smtp_conf);
    }

  /**
   * Updates the message with the settings configured in the model
   *
   * @return void
   */
    private function _updateInternalMessage()
    {
        $from         = trim($this->_generalSettings->getFrom());
        $from_name    = trim($this->_generalSettings->getFromName());
        $reply_to     = trim($this->_generalSettings->getReplyTo());
        $categories   = explode(',', $this->_generalSettings->getCategories());
        $template     = trim($this->_generalSettings->getTemplateID());
        $asm_group_id = trim($this->_generalSettings->getAsmGroupId());

        $xsmtpapi_header['category'] = [ 'magento2_sendgrid_plugin' ];
        foreach ($categories as $category) {
            $xsmtpapi_header['category'][] = trim($category);
        }

        if (! empty($template)) {
            $xsmtpapi_header['filters']['templates']['settings']['enable']      = 1;
            $xsmtpapi_header['filters']['templates']['settings']['template_id'] = $template;
        }

        // asm group id
        if ($asm_group_id != false and $asm_group_id != 0) {
            $xsmtpapi_header['asm_group_id'] = intval($asm_group_id);
        }

        $this->_message->addHeader('x-smtpapi', json_encode($xsmtpapi_header));

        if (! empty($from)) {
            $this->_message->clearFrom();
            $this->_message->setFrom($from);
        }

        if (! empty($from_name) and ! empty($from)) {
            $this->_message->clearFrom();
            $this->_message->setFrom($from, $from_name);
        }

        if (! empty($from_name) and empty($from)) {
            $initial_from = $this->_message->getFrom();
            $this->_message->clearFrom();
            $this->_message->setFrom($initial_from, $from_name);
        }

        if (! empty($reply_to)) {
            $this->_message->clearReplyTo();
            $this->_message->setReplyTo($reply_to);
        }
    }

  /**
   * Returns a string with the JSON request for the API from the current message
   *
   * @return string
   */
    private function _getAPIMessage()
    {
        // Model values
        $from         = trim($this->_generalSettings->getFrom());
        $from_name    = trim($this->_generalSettings->getFromName());
        $reply_to     = trim($this->_generalSettings->getReplyTo());
        $categories   = explode(',', $this->_generalSettings->getCategories());
        $template     = trim($this->_generalSettings->getTemplateID());
        $asm_group_id = trim($this->_generalSettings->getAsmGroupId());

        // Default category
        $categories[] = 'magento2_sendgrid_plugin';

        // Message values
        $recipients = $this->_message->getRecipients();
        $subject    = trim($this->_message->getSubject());
        $text       = $this->_message->getBodyText(false);
        $html       = $this->_message->getBodyHtml(false);

        if ($text instanceof \Zend_Mime_Part) {
            $text = $text->getRawContent();
        }

        if ($html instanceof \Zend_Mime_Part) {
            $html = $html->getRawContent();
        }

        // If no from field in model, get message from
        if (empty($from)) {
            $from = $this->_message->getFrom();
        }

        // If no reply to field in model, get message reply to
        if (empty($reply_to)) {
            $reply_to = $this->_message->getReplyTo();
        }

        // Initializations
        $mail = new \Zend_Mail();
        $personalization = new API\Personalization();

        // Add To's
        foreach ($recipients as $to) {
            $email = new \Zend_Mail(trim($to));
            $personalization->addTo($email);
        }

        // Add from with or without name
        if (! empty($from_name)) {
            $email = new API\Email($from_name, $from);
            $mail->setFrom($email);
        } else {
            $email = new API\Email(null, $from);
            $mail->setFrom($email);
        }

        // Plain content
        if (! empty($text)) {
            $content = new API\Content('text/plain', $text);
            $mail->addContent($content);
        }

        // HTML content
        if (! empty($html)) {
            $content = new API\Content('text/html', $html);
            $mail->addContent($content);
        }

        // Reply to
        if (! empty($reply_to)) {
            $email = new API\Email(null, $reply_to);
            $mail->setReplyTo($email);
        }

        // Categories
        foreach ($categories as $category) {
            if (! empty(trim($category))) {
                $mail->addCategory(trim($category));
            }
        }

        // Template ID
        if (! empty($template)) {
            $mail->setTemplateId($template);
        }

        $mail->setSubject($subject);
        $mail->addPersonalization($personalization);

        // Attachments
        $parts = $this->_message->getParts();
        foreach ($parts as $part) {
            $attachment = new API\Attachment();
            $attachment->setContent(base64_encode($part->getRawContent()));
            $attachment->setType($part->type);
            $attachment->setFilename($part->filename);
            $attachment->setDisposition($part->disposition);

            $mail->addAttachment($attachment);
        }

        // asm group id
        if ($asm_group_id != false and $asm_group_id != 0) {
            $asm = new API\ASM();
            $asm->setGroupId(intval($asm_group_id));

            $mail->setASM($asm);
        }

        return $mail->jsonSerialize();
    }

  /**
   * Sets the message
   *
   * @param   MessageInterface  $message
   * @return  void
   * @throws  \Magento\Framework\Exception\MailException
   */
    public function setMessage(MessageInterface $message)
    {
        if (! $message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }

        $this->_message = $message;
        $this->_updateInternalMessage();
    }

  /**
   * Send a mail using this transport
   *
   * @return void
   * @throws \Magento\Framework\Exception\MailException
   */
    public function sendMessage()
    {
        try {
            $this->_logger->debug('[SendGrid] Sending email.');

            $apikey = $this->_generalSettings->getAPIKey();
            $send_method = $this->_generalSettings->getSendMethod();

            if (! $this->_moduleManager->isOutputEnabled('SendGrid_EmailDeliverySimplified')) {
                $this->_logger->debug('[SendGrid] Module is not enabled. Email is sent via vendor Zend Mail.');
                parent::send($this->_message);

                return;
            }

            if ('smtp' == $send_method or empty(trim($apikey))) {
                parent::send($this->_message);
            } else {
                // Compose JSON payload of email send request
                $payload = $this->_getAPIMessage();

                // Mail send URL
                $url = Tools::SG_API_URL . 'v3/mail/send';

                // Request headers
                $headers = [ 'Authorization' => 'Bearer ' . $apikey ];

                // Send request
                $client = new \Zend_Http_Client($url, [ 'strict' => true ]);

                $response = $client->setHeaders($headers)
                           ->setRawData(json_encode($payload), 'application/json')
                           ->request('POST');

                // Process response
                if (202 != $response->getStatus()) {
                    $response = $response->getBody();

                    throw new \Exception($response);
                }
            }
        } catch (\Exception $e) {
            $this->_logger->debug('[SendGrid] Error sending email : ' . $e->getMessage());
            throw new MailException(new Phrase($e->getMessage()), $e);
        }
    }

  /**
   * Get message
   *
   * @return string
   */
    public function getMessage()
    {
        return $this->_message;
    }
}
