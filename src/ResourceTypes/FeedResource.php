<?php


namespace PinaCMS\ResourceTypes;


use Pina\Controls\Nav;
use Pina\Data\DataTable;
use Pina\Request;
use PinaCMS\Controls\Feed;
use PinaCMS\Controls\FeedRecordRow;
use PinaCMS\Controls\Page;
use PinaCMS\SQL\ArticleGateway;
use PinaCMS\SQL\FeedGateway;
use PinaCMS\SQL\ResourceUrlGateway;
use PinaDashboard\Dashboard;
use PinaCMS\Endpoints\ArticleEndpoint;
use PinaCMS\ResourceTypeInterface;
use PinaCMS\SQL\ResourceGateway;
use Pina\App;
use Pina\Controls\Control;
use Pina\Http\Location;

use PinaMedia\Media;
use PinaMedia\MediaGateway;

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
     * @throws \Exception
     */
    public function draw(int $id): Control
    {
        $feed = FeedGateway::instance()
            ->whereBy('enabled', 'Y')
            ->select('title')
            ->innerJoin(
                ResourceGateway::instance()->on('id', 'id')
                    ->onBy('enabled', 'Y')
                    ->select('meta_title')
                    ->select('meta_description')
                    ->select('meta_keywords')
            )
            ->leftJoin(
                MediaGateway::instance()
                    ->on('id', 'media_id')
                    ->selectAs('path', 'media_path')
                    ->selectAs('storage', 'media_storage')
            )
            ->innerJoin(
                ResourceUrlGateway::instance()->on('id', 'id')
                    ->select('url')
            )
            ->find($id);

        /** @var Page $view */
        $view = App::make(Page::class);
        $view->setTitle($feed['title']);

        Request::setPlace('page_header', $feed['title']);
        Request::setPlace('meta_title', !empty($feed['meta_title']) ? $feed['meta_title'] : $feed['title']);
        Request::setPlace('meta_description', $feed['meta_description']);
        Request::setPlace('meta_keywords', $feed['meta_keywords']);

        Request::setPlace('canonical', $feed['url']);

        Request::setPlace('og_type', 'article');
        Request::setPlace('og_title', !empty($feed['meta_title']) ? $feed['meta_title'] : $feed['title']);
        Request::setPlace('og_description', $feed['meta_description']);
        if (!empty($feed['media_storage']) && !empty($feed['media_path'])) {
            Request::setPlace('og_image', Media::getUrl($feed['media_storage'], $feed['media_path']));
        } else {
            Request::setPlace('og_image', '');
        }

        $query = ArticleGateway::instance()
            ->orderBy('created_at', 'desc')
            ->whereBy('feed_id', $id)
            ->whereBy('enabled', 'Y')
            ->select('title')
            ->select('text')
            ->select('media_id')
            ->innerJoin(
                ResourceUrlGateway::instance()->on('id', 'id')
                    ->select('url')
            )
            ->leftJoin(
                MediaGateway::instance()
                    ->on('id', 'media_id')
                    ->selectAs('path', 'media_path')
                    ->selectAs('storage', 'media_storage')
            );

        $data = new DataTable($query->get(), $query->getQuerySchema());

        $container = App::make(Feed::class);

        $nav = App::make(Nav::class);
        $nav->addClass('feed');
        foreach ($data as $record) {
            /** @var FeedRecordRow $row */
            $row = App::make(FeedRecordRow::class);
            $row->load($record);
            $row->setLink($record->getData()['url']);
            $row->setIgnore(['url', 'media_path', 'media_storage', 'media_id']);

            $nav->append($row);
        }
        $container->append($nav);
        return $container;
    }

    public function getEditLocation(int $id): Location
    {
        /** @var Dashboard $dashboard */
        $dashboard = App::load(Dashboard::class);
        return $dashboard->getEndpointLocation(ArticleEndpoint::class)->location('@/:id', ['id' => $id]);
    }

}