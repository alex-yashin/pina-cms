<?php


namespace PinaCMS\Controls;


use Pina\App;
use Pina\Controls\Control;
use Pina\Html;
use PinaCMS\Model\Article;

class ArticleView extends Control
{
    /** @var Article */
    protected $article;

    public function load(Article $article)
    {
        $this->article = $article;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function draw()
    {
        App::assets()->addScript('/article.js');
        return Html::zz(
            'main.container section(header(h1%)+.article%)',
            $this->article->getTitle(),
            $this->drawInnerBefore() . $this->drawInner() . $this->drawInnerAfter()
        );
    }

    protected function drawInner()
    {
        return $this->article->getText();
    }

}