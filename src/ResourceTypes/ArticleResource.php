<?php

namespace PinaCMS\ResourceTypes;

use Pina\Request;
use PinaCMS\Controls\Page;
use PinaDashboard\Dashboard;
use PinaCMS\Endpoints\ArticleEndpoint;
use PinaCMS\ResourceTypeInterface;
use PinaCMS\SQL\ArticleGateway;
use PinaCMS\SQL\ResourceGateway;
use Pina\App;
use Pina\Controls\Control;
use Pina\Controls\RawHtml;
use Pina\Data\Schema;
use Pina\Http\Location;
use Pina\ResourceManagerInterface;
use Pina\StaticResource\Style;

use PinaMedia\Media;
use PinaMedia\MediaGateway;

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
     * @throws \Exception
     */
    public function draw(int $id): Control
    {
        $resources = App::container()->get(ResourceManagerInterface::class);
        $resources->append((new Style())->setSrc('static/default/css/editor-content.css'));
        $article = ArticleGateway::instance()
            ->whereBy('enabled', 'Y')
            ->select('text')
            ->select('title')
            ->innerJoin(
                ResourceGateway::instance()->on('id', 'id')
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
            ->findOrFail($id);

        /** @var Page $view */
        $view = App::make(Page::class);
        $view->setTitle($article['title']);
        $view->append((new RawHtml())->setText($article['text']));

        Request::setPlace('page_header', $article['title']);
        Request::setPlace('meta_title', !empty($article['meta_title']) ? $article['meta_title'] : $article['title']);
        Request::setPlace('meta_description', $article['meta_description']);
        Request::setPlace('meta_keywords', $article['meta_keywords']);

        Request::setPlace('og_type', 'article');
        Request::setPlace('og_title', !empty($article['meta_title']) ? $article['meta_title'] : $article['title']);
        Request::setPlace('og_description', $article['meta_description']);
        if (!empty($article['media_storage']) && !empty($article['media_path'])) {
            Request::setPlace('og_image', Media::getUrl($article['media_storage'], $article['media_path']));
        } else {
            Request::setPlace('og_image', '');
        }

        return $view;
    }

    /**
     * @return Schema
     * @throws \Exception
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