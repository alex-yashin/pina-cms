<?php


namespace PinaCMS;

use PinaCMS\Endpoints\ArticleEndpoint;
use PinaCMS\Endpoints\FeedEndpoint;
use PinaCMS\Endpoints\ResourceManagementEndpoint;
use PinaCMS\Endpoints\SitemapEndpoint;
use PinaCMS\ResourceTypes\ArticleResource;
use PinaCMS\ResourceTypes\FeedResource;
use PinaDashboard\Dashboard;
use PinaMedia\Endpoints\UploadEndpoint;
use Pina\Access;
use Pina\App;
use Pina\DispatcherRegistry;
use Pina\ModuleInterface;
use PinaCMS\Endpoints\ResourceEndpoint;
use PinaMedia\Media;

class Module implements ModuleInterface
{

    public function __construct()
    {
        App::modules()->load(\PinaTime\Module::class);

        /** @var ResourceTypeFactory $factory */
        $factory = App::load(ResourceTypeFactory::class);
        $factory->register('feed', FeedResource::class);
        $factory->register('article', ArticleResource::class);
    }

    public function getPath()
    {
        return __DIR__;
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getTitle()
    {
        return 'CMS';
    }

    public function http()
    {
        App::router()->register('rs', ResourceEndpoint::class);
        Access::permit('rs', 'public');

        App::router()->register('sitemap', SitemapEndpoint::class);
        Access::permit('sitemap', 'public');

        /** @var Dashboard $dashboard */
        $dashboard = App::load(Dashboard::class);
        $section = $dashboard->section('CMS');
        $section->register('resources', ResourceManagementEndpoint::class);
        $section->register('articles', ArticleEndpoint::class);
        $section->register('feeds', FeedEndpoint::class);
        $section->register('upload', UploadEndpoint::class);

        DispatcherRegistry::register(new Dispatcher());

        Media::allowMimeType('application/zip');

        return [];
    }

}