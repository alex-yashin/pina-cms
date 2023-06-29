<?php


namespace PinaCMS\Controls;


use Exception;
use Pina\Controls\Control;
use Pina\Html;
use PinaCMS\Model\Article;

class FeedArticleItemView extends Control
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
    protected function draw()
    {
        return Html::tag(
            'li',
            Html::a(
                $this->drawInnerBefore() . $this->drawInner() . $this->drawInnerAfter(),
                $this->article->getLink()
            ),
            $this->makeAttributes()
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function drawInner()
    {
        $text = mb_substr(strip_tags($this->article->getText()), 0, 500);

        $mediaUrl = $this->article->getMedia()->getUrl();
        $icon = $mediaUrl ? Html::img($mediaUrl, ['class' => 'image']) : '';

        $content = [];
        $content[] = Html::tag('header', $this->article->getTitle());

        $tags = implode(' • ', $this->article->getLabels());
        $second = $tags ? Html::tag('i', $tags) : '';
        if ($text && mb_strlen($text) > 10) {
            $second .= ' — ' . $text;
        }
        $content[] = Html::nest('.short',  $second);

        return $icon . implode('', array_filter($content));
    }

}