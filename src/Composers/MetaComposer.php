<?php

namespace PinaCMS\Composers;

use Pina\Request;
use PinaCMS\Model\Resource;

class MetaComposer
{

    /**
     * @param string $ogType
     * @param Resource $resource
     * @throws \Exception
     */
    public function set(string $ogType, Resource $resource)
    {
        Request::setPlace('page_header', $resource->getTitle());
        Request::setPlace('meta_title', $resource->getMetaTitle());
        Request::setPlace('meta_description', $resource->getMetaDescription());
        Request::setPlace('meta_keywords', $resource->getMetaKeywords());

        Request::setPlace('canonical', $resource->getLink());

        Request::setPlace('og_type', $ogType);
        Request::setPlace('og_title', $resource->getMetaTitle());
        Request::setPlace('og_description', $resource->getMetaDescription());
        Request::setPlace('og_image', $resource->getMedia()->getUrl());
    }

}