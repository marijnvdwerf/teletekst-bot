<?php

namespace schellie\teletekstbot\telegram;

use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class PhotoMessage extends Message
{
    private $path;
    private $caption;

    public function __construct($path, $caption = null)
    {
        $this->path = $path;
        $this->caption = $caption;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest($chatID)
    {
        $components = [
            [
                'name' => 'chat_id',
                'contents' => (string)$chatID
            ],
            [
                'name' => 'photo',
                'contents' => fopen($this->path, 'rb')
            ]
        ];

        if (!empty(trim($this->caption))) {
            $components[] = [
                'name' => 'caption',
                'contents' => $this->caption
            ];
        }

        if ($this->hasReplyMarkup()) {
            $components[] = [
                'name' => 'reply_markup',
                'contents' => json_encode($this->getReplyMarkup())
            ];
        }

        if ($this->isReply()) {
            $components[] = [
                'name' => 'reply_to_message_id',
                'contents' => (string)$this->getReplyToMessageID()
            ];
        }

        $body = new MultipartStream($components);

        return new Request(
            'POST',
            'sendPhoto',
            ['Content-Type' => 'multipart/form-data; boundary=' . $body->getBoundary()],
            $body);
    }
}
