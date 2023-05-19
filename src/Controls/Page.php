<?php

namespace PinaCMS\Controls;

use Pina\App;
use Pina\Controls\Control;
use Pina\Html;
use Pina\ResourceManagerInterface;
use Pina\StaticResource\Script;

class Page extends Control
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
        $this->resources()->append((new Script())->setSrc('article.js'));
        return Html::zz(
            'main.container section(header(h1%)+.article%)',
            $this->title,
            $this->drawInnerBefore() . $this->drawInner() . $this->drawInnerAfter()
        );
    }

    /**
     * @return ResourceManagerInterface
     */
    protected function resources()
    {
        return App::container()->get(ResourceManagerInterface::class);
    }

}