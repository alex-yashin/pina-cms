<?php


namespace PinaCMS\Controls;


use Pina\App;
use Pina\Controls\Control;
use Pina\Html;
use PinaCMS\Model\Article;
use PinaCMS\Model\Feed;

class FeedView extends Control
{

    /** @var Feed */
    protected $feed;

    /** @var Article[] */
    protected $articles = [];

    public function load(Feed $feed, array $articles)
    {
        $this->feed = $feed;
        $this->articles = $articles;
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
            $this->feed->getTitle(),
            $this->drawInnerBefore() . $this->drawInner() . $this->drawInnerAfter()
        );
    }

    protected function drawInner()
    {
        $r = '';
        foreach ($this->articles as $article) {
            /** @var FeedArticleItemView $itemView */
            $itemView = App::make(FeedArticleItemView::class);
            $itemView->load($article);
            $r .= $itemView;
        }
        return Html::nest('ul.nav feed', $r);
    }

}