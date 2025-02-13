<?php


namespace PinaCMS\ResourceTypes;


use Exception;
use Pina\Paging;
use PinaCMS\Composers\MetaComposer;
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

        /** @var MetaComposer $composer */
        $composer = App::make(MetaComposer::class);
        $composer->set('article', $feed, $id);

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