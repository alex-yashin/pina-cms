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

use function Pina\__;

class ArticleResource implements ResourceTypeInterface
{

    public function getTitle(): string
    {
        return __('Статья');
    }

    public function draw(int $id): Control
    {
        $resources = App::container()->get(ResourceManagerInterface::class);
        $resources->append((new Style())->setSrc('static/default/css/editor-content.css'));
        $article = ArticleGateway::instance()
            ->select('text')
            ->innerJoin(
                ResourceGateway::instance()->on('id', 'id')
                    ->onBy('enabled', 'Y')
                    ->select('title')
            )
            ->findOrFail($id);

        /** @var Page $view */
        $view = App::make(Page::class);
        $view->setTitle($article['title']);
        $view->append((new RawHtml())->setText($article['text']));

        Request::setPlace('page_header', $article['title']);

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