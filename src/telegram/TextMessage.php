<?php

namespace schellie\teletekstbot\telegram;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

class TextMessage extends Message
{
    private $text;

    public function __construct($text)
    {
        if (is_array($text)) {
            $text = implode(PHP_EOL, $text);
        }

        $this->text = $text;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest($chatID)
    {
        $uri = new Uri('sendMessage');
        $uri = Uri::withQueryValue($uri, 'chat_id', $chatID);
        $uri = Uri::withQueryValue($uri, 'text', utf8_encode($this->text));

        if ($this->hasReplyMarkup() !== null) {
            $uri = Uri::withQueryValue($uri, 'reply_markup', json_encode($this->getReplyMarkup()));
        }

        if ($this->isReply()) {
            $uri = Uri::withQueryValue($uri, 'reply_to_message_id', $this->getReplyToMessageID());
        }

        return new Request('GET', $uri);
    }
}
