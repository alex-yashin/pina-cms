<?php


namespace PinaCMS;

use Pina\Router;
use PinaCMS\Endpoints\ArticleEndpoint;
use PinaCMS\Endpoints\FeedEndpoint;
use PinaCMS\Endpoints\ResourceManagementEndpoint;
use PinaCMS\Endpoints\SitemapEndpoint;
use PinaCMS\Menu\ArticleMenu;
use PinaCMS\ResourceTypes\ArticleResource;
use PinaCMS\ResourceTypes\FeedResource;
use PinaDashboard\Dashboard;
use PinaMedia\Endpoints\UploadEndpoint;
use Pina\App;
use Pina\ModuleInterface;
use PinaCMS\Endpoints\ResourceEndpoint;
use PinaMedia\Media;
use Pina\Menu\MainMenu;

class Module implements ModuleInterface
{

    public function __construct()
    {
        App::modules()->load(\PinaTime\Module::class);

        /** @var ResourceTypeFactory $factory */
        $factory = App::load(ResourceTypeFactory::class);
        $factory->register('feed', FeedResource::class);
        $factory->register('article', ArticleResource::class);
        
        App::onLoad(Router::class, function(Router $router) {
            $router->register('rs', ResourceEndpoint::class)->permit('public');
            $router->register('sitemap', SitemapEndpoint::class)->permit('public');

            /** @var MainMenu $mainMenu */
            $mainMenu = App::load(MainMenu::class);
            $articleMenu = App::load(ArticleMenu::class);

            /** @var Dashboard $dashboard */
            $router->register('articles', ArticleEndpoint::class)->permit('root')->addToMenu($mainMenu)->addToMenu($articleMenu);
            $router->register('feeds', FeedEndpoint::class)->permit('root')->addToMenu($articleMenu);
            $router->register('resources', ResourceManagementEndpoint::class)->permit('root')->addToMenu($articleMenu);

            $router->register('upload', UploadEndpoint::class);

            $router->registerDispatcher(new Dispatcher());

            Media::allowMimeType('application/zip');
        });
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

}