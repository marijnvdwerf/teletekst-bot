<?php

namespace schellie\teletekstbot\commands;

use schellie\teletekstbot\NavigationStatus;

class DirectNavigationCommand implements NavigationalCommand
{
    private $page;

    public function __construct($page)
    {
        $this->page = $page;
    }

    public function getTargetPageID(NavigationStatus $currentPage = null)
    {
        return $this->page;
    }
}
