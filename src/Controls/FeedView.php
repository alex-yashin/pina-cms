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
        App::assets()->addScript('/article.js');
        return Html::zz(
            'main.container section(%+ul.nav feed%+%+%)',
            $this->drawInnerBefore(),
            $this->drawInner(),
            $this->drawInnerAfter(),
            $this->drawPaging()
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
        return $r;
    }

    protected function drawPaging()
    {
        /** @var PagingControl $pagingControl */
        $pagingControl = App::make(PagingControl::class);
        $pagingControl->init($this->paging);

        return $pagingControl;
    }

}