<?php

namespace Eadesigndev\Pdfgenerator\Model\Email;

use Magento\Framework\Mail\MailMessageInterface;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;

/**
 * Class Message
 * @package Eadesigndev\Pdfgenerator\Model\Email
 * @deprecated
 */
class Message extends \Magento\Framework\Mail\Message implements MailMessageInterface
{

    /**
     * @var \Laminas\Mail\Message
     */
    protected $zendMessage;

    private $attachment;

    private $messageType = Mime::TYPE_TEXT;

    public function __construct(
        $charset = 'utf-8'
    ) {
        $this->zendMessage = new \Laminas\Mail\Message();
        $this->zendMessage->setEncoding($charset);
		$this->attachment=[];
    }

    public function setBodyAttachment($content, $fileName, ?string $fileType='application/pdf')
    {
        $attachmentPart = new Part($body);
		$attachmentPart->setType($fileType)
            ->setEncoding(Mime::ENCODING_BASE64)
            ->setFileName($fileName)
            ->setDisposition(Mime::DISPOSITION_ATTACHMENT);

        $this->attachment[] = $attachmentPart;
        return $this;
    }

    public function setMessageType($type)
    {
        $this->messageType = $type;
        return $this;
    }

    public function setBody($body)
    {
        if (is_string($body) && $this->messageType === MailMessageInterface::TYPE_HTML) {
            $body = self::createHtmlMimeFromString($body);
        }

		foreach ($this->attachment as $attachment) {
            $body->addPart($attachment);
        }
        $this->zendMessage->setBody($body);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject)
    {
        $this->zendMessage->setSubject($subject);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->zendMessage->getSubject();
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        return $this->zendMessage->getBody();
    }

    /**
     * {@inheritdoc}
     */
    public function setFromAddress($fromAddress, $fromName = null)
    {
        $this->zendMessage->setFrom($fromAddress, $fromName);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addTo($toAddress)
    {
        $this->zendMessage->addTo($toAddress);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addCc($ccAddress)
    {
        $this->zendMessage->addCc($ccAddress);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addBcc($bccAddress)
    {
        $this->zendMessage->addBcc($bccAddress);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setReplyTo($replyToAddress)
    {
        $this->zendMessage->setReplyTo($replyToAddress);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawMessage()
    {
        return $this->zendMessage->toString();
    }

    private function createHtmlMimeFromString($htmlBody)
    {
        $htmlPart = new Part(['content' => $htmlBody]);
        $htmlPart->setCharset($this->zendMessage->getEncoding());
        $htmlPart->setType(Mime::TYPE_HTML);
        $mimeMessage = $this->mimeMessageFactory->create();
        $mimeMessage->addPart($htmlPart);
        return $mimeMessage;
    }

     /**
     * {@inheritdoc}
	 * !!!! MKS Fix for broken DKIM
     */
    public function setBodyHtml($html)
    {
        $this->setMessageType(self::TYPE_HTML);
		$html=wordwrap($html,800,"\r\n",true);
        return $this->setBody($html);
    }

    /**
     * {@inheritdoc}
     */
    public function setBodyText($text)
    {
        $this->setMessageType(self::TYPE_TEXT);
        return $this->setBody($text);
    }
}
