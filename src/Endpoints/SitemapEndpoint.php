<?php


namespace PinaCMS\Endpoints;


use Pina\App;
use Pina\Http\Endpoint;
use PinaCMS\SQL\ResourceGateway;
use PinaCMS\SQL\ResourceUrlGateway;

class SitemapEndpoint extends Endpoint
{

    public function index()
    {
        $resources = ResourceGateway::instance()
            ->whereBy('enabled', 'Y')
            ->innerJoin(
                ResourceUrlGateway::instance()
                    ->on('id', 'id')
                    ->select('url')
            )
            ->get();

        header("Content-type: text/xml");
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        echo '<url><loc>' . App::link('/') . '</loc></url>';
        foreach ($resources as $resource) {
            echo '<url><loc>' . App::link($resource['url']) . '</loc></url>';
        }

        echo '</urlset> ';
        exit;
    }

}