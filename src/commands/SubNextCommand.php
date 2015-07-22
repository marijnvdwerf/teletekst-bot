<?php

namespace schellie\teletekstbot\commands;

use schellie\teletekstbot\NavigationStatus;

class SubNextCommand extends Command implements NavigationalCommand
{
    public function getTargetPageID(NavigationStatus $status = null)
    {
        return $status->nextSubPage;
    }
}
