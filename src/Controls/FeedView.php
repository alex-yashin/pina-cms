<?php


namespace PinaCMS\Controls;


use Exception;
use Pina\App;
use Pina\Controls\Control;
use Pina\Controls\PagingControl;
use Pina\Html;
use Pina\Paging;
use PinaCMS\Model\Article;
use PinaCMS\Model\Feed;

class FeedView extends Control
{

    /** @var Feed */
    protected $feed;

    /** @var Article[] */
    protected $articles = [];

    /** @var Paging */
    protected $paging;

    public function load(Feed $feed, array $articles, Paging $paging)
    {
        $this->feed = $feed;
        $this->articles = $articles;
        $this->paging = $paging;
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function draw()
    {
        return Html::nest(
            'main.container section',
            $this->drawInnerBefore() . $this->drawInner() .  $this->drawInnerAfter() . $this->drawPaging(),
            $this->makeAttributes()
        );
    }

    protected function drawInner()
    {
        $r = '';
        foreach ($this->articles as $article) {
            /** @var FeedArticleView $itemView */
            $itemView = App::make(FeedArticleView::class);
            $itemView->load($article);
            $r .= $itemView;
        }
        return Html::nest('ul.nav feed', $r);
    }

    protected function drawPaging()
    {
        /** @var PagingControl $pagingControl */
        $pagingControl = App::make(PagingControl::class);
        $pagingControl->init($this->paging);

        return $pagingControl;
    }

}