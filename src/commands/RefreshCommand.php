<?php

namespace schellie\teletekstbot\commands;

use schellie\teletekstbot\commands;
use schellie\teletekstbot\NavigationStatus;

class RefreshCommand extends Command implements NavigationalCommand
{
    public function getTargetPageID(NavigationStatus $status = null)
    {
        return $status->currentPage;
    }
}

