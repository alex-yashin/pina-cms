<?php


namespace PinaCMS\Model;


use Pina\Model\LinkedItem;

class Resource extends LinkedItem
{
    public function __construct($title, $link)
    {
        parent::__construct($title, $link);
    }
}