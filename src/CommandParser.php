<?php

namespace schellie\teletekstbot;

use schellie\teletekstbot\commands\DirectNavigationCommand;

class CommandParser
{
    private $botname;

    public function __construct($botname)
    {
        $this->botname = $botname;
    }

    public function parseCommand($body)
    {
        $body = str_replace('/', '', $body);
        $body = str_replace('@' . $this->botname, '', $body);
        $body = trim($body);
        $body = strtolower($body);

        if (strpos($body, 'start') === 0) {
            return new commands\WelcomeCommand();
        }

        if (strpos($body, 'help') === 0) {
            return new commands\WelcomeCommand();
        }

        if (strpos($body, 'about') === 0) {
            return new commands\AboutCommand();
        }

        if (strpos($body, 'stats') === 0) {
            return new commands\StatsCommand();
        }

        if (strpos($body, 'vorige') === 0 || strpos($body, '<<') === 0) {
            return new commands\PrevCommand();
        }

        if (strpos($body, 'volgende') === 0 || strpos($body, '>>') === 0) {
            return new commands\NextCommand();
        }

        if (strpos($body, 'subvorige') === 0 || strpos($body, '<') === 0) {
            return new commands\SubPrevCommand();
        }

        if (strpos($body, 'subvolgende') === 0 || strpos($body, '>') === 0) {
            return new commands\SubNextCommand();
        }

        if (strpos($body, 'herladen') === 0) {
            return new commands\RefreshCommand();
        }

        if (preg_match('/(?<page>\d{3}(\-\d+)?)/', $body, $matches)) {
            return new DirectNavigationCommand($matches['page']);
        }

        return null;
    }
}
