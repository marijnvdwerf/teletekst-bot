<?php

namespace schellie\teletekstbot;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class TeletekstClient
{

    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getPage($page)
    {
        try {
            $response = $this->client->get('http://teletekst-data.nos.nl/json/' . $page);
            return json_decode($response->getBody());
        } catch (ClientException $e) {
            // error
        }

        return null;
    }
}
