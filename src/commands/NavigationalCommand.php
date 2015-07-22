<?php

namespace schellie\teletekstbot\commands;

use schellie\teletekstbot\commands;
use schellie\teletekstbot\NavigationStatus;

interface NavigationalCommand
{
    public function getTargetPageID(NavigationStatus $status = null);
}
