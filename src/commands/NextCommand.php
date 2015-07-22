<?php

namespace schellie\teletekstbot\commands;

use schellie\teletekstbot\NavigationStatus;

class NextCommand extends Command implements NavigationalCommand
{
    public function getTargetPageID(NavigationStatus $status = null)
    {
        return $status->nextPage;
    }
}
