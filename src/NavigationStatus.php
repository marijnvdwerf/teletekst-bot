<?php

namespace schellie\teletekstbot;

class NavigationStatus
{
    public $currentPage;
    public $prevPage;
    public $nextPage;
    public $prevSubPage;
    public $nextSubPage;

    public static function fromPage($newPageID, $page)
    {
        $status = new NavigationStatus();
        $status->currentPage = $newPageID;
        $status->prevPage = $page->prevPage;
        $status->prevSubPage = $page->prevSubPage;
        $status->nextSubPage = $page->nextSubPage;
        $status->nextPage = $page->nextPage;

        return $status;
    }

    public static function fromJSON(\StdClass $json)
    {
        $status = new NavigationStatus();
        foreach (get_object_vars($json) as $key => $value) {
            $status->{$key} = $value;
        }

        return $status;
    }
}
