<?php

namespace PinaCMS\ResourceTypes;

use Exception;
use Pina\Request;
use PinaCMS\Controls\ArticleView;
use PinaDashboard\Dashboard;
use PinaCMS\Endpoints\ArticleEndpoint;
use PinaCMS\ResourceTypeInterface;
use PinaCMS\SQL\ArticleGateway;
use Pina\App;
use Pina\Controls\Control;
use Pina\Data\Schema;
use Pina\Http\Location;

use function Pina\__;

class ArticleResource implements ResourceTypeInterface
{

    public function getTitle(): string
    {
        return __('Статья');
    }

    /**
     * @param int $id
     * @return Control
     * @throws Exception
     */
    public function draw(int $id): Control
    {
        $article = ArticleGateway::instance()
            ->selectArticleFields()
            ->findArticleOrFail($id);

        /** @var ArticleView $view */
        $view = App::make(ArticleView::class);
        $view->load($article);

        Request::setPlace('page_header', $article->getTitle());
        Request::setPlace('meta_title', $article->getMetaTitle());
        Request::setPlace('meta_description', $article->getMetaDescription());
        Request::setPlace('meta_keywords', $article->getMetaKeywords());

        Request::setPlace('canonical', $article->getLink());

        Request::setPlace('og_type', 'article');
        Request::setPlace('og_title', $article->getMetaTitle());
        Request::setPlace('og_description', $article->getMetaDescription());
        Request::setPlace('og_image', $article->getMedia()->getUrl());

        return $view;
    }

    /**
     * @return Schema
     * @throws Exception
     */
    public function getSchema(): Schema
    {
        return ArticleGateway::instance()->getSchema()->forgetField('id');
    }

    public function getEditLocation(int $id): Location
    {
        /** @var Dashboard $dashboard */
        $dashboard = App::load(Dashboard::class);
        return $dashboard->getEndpointLocation(ArticleEndpoint::class)->location('@/:id', ['id' => $id]);
    }
}