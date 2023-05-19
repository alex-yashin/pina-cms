<?php


namespace PinaCMS;

use PinaCMS\ResourceTypes\ArticleResource;
use PinaCMS\ResourceTypes\FeedResource;
use PinaCMS\SQL\ResourceTypeGateway;
use Pina\InstallationInterface;

class Installation implements InstallationInterface
{

    public function prepare()
    {
    }

    public function install()
    {
        self::createTypes();
    }

    public function createTypes()
    {
        $this->createType(ArticleResource::class);
        $this->createType(FeedResource::class);
    }

    public function createType($type)
    {
        if (ResourceTypeGateway::instance()->whereBy('class', $type)->exists()) {
            return;
        }

        ResourceTypeGateway::instance()->insert(['class' => $type]);
    }

    public function remove()
    {
    }

}
