<?php


namespace PinaCMS\Controls;


use Pina\Controls\Control;
use Pina\Html;

class FeedItem extends Control
{
    protected $title = '';

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function draw()
    {
        return Html::zz('h2%+article%', $this->title, $this->drawInnerBefore() . $this->drawInner() . $this->drawInnerAfter());
    }

}