<?php

namespace schellie\teletekstbot\telegram;

class Message
{
    private $keyboard;
    private $replyTo;

    public function withReplyMarkup($keyboard)
    {
        $this->keyboard = $keyboard;
        return $this;
    }


    protected function hasReplyMarkup()
    {
        return !is_null($this->keyboard);
    }

    protected function getReplyMarkup()
    {
        return [
            'keyboard' => $this->keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
    }

    public function setReplyToMessageID($messageID)
    {
        $this->replyTo = $messageID;
        return $this;
    }

    protected function isReply()
    {
        return !is_null($this->replyTo);
    }

    protected function getReplyToMessageID()
    {
        return $this->replyTo;
    }
}
