<?php

namespace PinaCMS\Composers;

use Pina\App;
use Pina\Controls\Meta;
use Pina\Request;
use PinaCMS\Model\Resource;

class MetaComposer
{

    /**
     * @param string $ogType
     * @param Resource $resource
     * @throws \Exception
     */
    public function set(string $ogType, Resource $resource): Meta
    {
        Request::setPlace('page_header', $resource->getTitle());
        Request::setPlace('meta_title', $resource->getMetaTitle());
        Request::setPlace('meta_description', $resource->getMetaDescription());
        Request::setPlace('meta_keywords', $resource->getMetaKeywords());

        Request::setPlace('canonical', $resource->getLink());

        /** @var Meta $meta */
        $meta = App::load(Meta::class);
        $meta->set('description', $resource->getMetaDescription());
        $meta->set('keywords', $resource->getMetaKeywords());
        $meta->set('og:type', $ogType);
        $meta->set('og:title', $resource->getMetaTitle());
        $meta->set('og:description', $resource->getMetaDescription());
        $meta->set('og:url', App::link($resource->getLink()));
        $meta->set('og:image', $resource->getMedia()->getUrl());
        return $meta;
    }

}