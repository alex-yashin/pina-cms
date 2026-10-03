<?php


namespace PinaCMS\Controls;


use Exception;
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
     * @throws Exception
     */
    protected function draw(): string
    {
        return Html::nest(
            'main.container section',
            $this->drawContent(),
            $this->makeAttributes()
        );
    }

    protected function drawContent()
    {
        return $this->article->getText();
    }

}