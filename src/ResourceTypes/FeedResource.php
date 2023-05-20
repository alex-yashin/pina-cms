<?php


namespace PinaCMS\ResourceTypes;


use PinaCMS\Controls\Feed;
use PinaCMS\Controls\FeedItem;
use PinaDashboard\Dashboard;
use PinaCMS\Endpoints\ArticleEndpoint;
use PinaCMS\ResourceTypeFactory;
use PinaCMS\ResourceTypeInterface;
use PinaCMS\SQL\ResourceGateway;
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
     * @throws \Exception
     */
    public function draw(int $id): Control
    {
        $container = App::make(Feed::class);
        $resources = ResourceGateway::instance()->whereBy('parent_id', $id)->get();
        foreach ($resources as $r) {
            /** @var FeedItem $item */
            $item = App::make(FeedItem::class);
            $item->setTitle($r['title']);

            /** @var ResourceTypeFactory $factory */
            $factory = App::load(ResourceTypeFactory::class);

            $item->append($factory->make($r['id'])->draw($r['id']));
            $container->append($item);
        }
        return $container;
    }

    public function getEditLocation(int $id): Location
    {
        /** @var Dashboard $dashboard */
        $dashboard = App::load(Dashboard::class);
        return $dashboard->getEndpointLocation(ArticleEndpoint::class)->location('@/:id', ['id' => $id]);
    }

}