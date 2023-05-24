<?php


namespace PinaCMS\Controls;


use Pina\Controls\Control;
use Pina\Html;

class Feed extends Control
{

    protected function draw()
    {
        return Html::nest('.container section',  $this->drawInnerBefore() . $this->drawInner() . $this->drawInnerAfter(), $this->getAttributes());
    }

}