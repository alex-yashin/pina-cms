<?php

namespace PinaCMS\Model;

use Pina\Model\LinkedItem;

class Article extends LinkedItem
{

    protected $text = '';

    public function __construct($title, $text, $link)
    {
        $this->text = $text;
        parent::__construct($title, $link);
    }

    public function getText()
    {
        return $this->text;
    }

}