<?php


namespace PinaCMS\ResourceTypes;


use Exception;
use Pina\Paging;
use Pina\Request;
use PinaCMS\Controls\FeedView;
use PinaCMS\SQL\ArticleGateway;
use PinaCMS\SQL\FeedGateway;
use PinaDashboard\Dashboard;
use PinaCMS\Endpoints\ArticleEndpoint;
use PinaCMS\ResourceTypeInterface;
use Pina\App;
use Pina\Controls\Control;
use Pina\Http\Location;

use function Pina\__;

class FeedResource implements ResourceTypeInterface
{

    public function getTitle(): string
    {
        return __('Лента');
    }

    /**
     * @param int $id
     * @return Control
     * @throws Exception
     */
    public function draw(int $id): Control
    {
        $feed = FeedGateway::instance()
            ->whereBy('enabled', 'Y')
            ->findFeedOrFail($id);

        Request::setPlace('page_header', $feed->getTitle());
        Request::setPlace('meta_title', $feed->getMetaTitle());
        Request::setPlace('meta_description', $feed->getMetaDescription());
        Request::setPlace('meta_keywords', $feed->getMetaKeywords());

        Request::setPlace('canonical', $feed->getLink());

        Request::setPlace('og_type', 'article');
        Request::setPlace('og_title', $feed->getMetaTitle());
        Request::setPlace('og_description', $feed->getMetaDescription());
        Request::setPlace('og_image', $feed->getMedia()->getUrl());

        $paging = new Paging($_GET['page'] ?? 1, 12);

        $articles = ArticleGateway::instance()
            ->wherePublished()
            ->orderBy('published_at', 'desc')
            ->whereBy('feed_id', $id)
            ->paging($paging)
            ->getArticles();

        /** @var FeedView $view */
        $view = App::make(FeedView::class);
        $view->load($feed, $articles, $paging);

        return $view;
    }

    public function getEditLocation(int $id): Location
    {
        /** @var Dashboard $dashboard */
        $dashboard = App::load(Dashboard::class);
        return $dashboard->getEndpointLocation(ArticleEndpoint::class)->location('@/:id', ['id' => $id]);
    }

}