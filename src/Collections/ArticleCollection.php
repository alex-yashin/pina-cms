<?php

namespace PinaCMS\Collections;

use PinaCMS\SQL\ArticleGateway;
use Pina\Data\Schema;

class ArticleCollection extends ResourceBasedCollection
{
    function makeQuery()
    {
        return ArticleGateway::instance()->orderBy('created_at', 'desc');
    }

    public function getListSchema($context = []): Schema
    {
        return parent::getListSchema()
            ->fieldset(['id', 'title', 'text', 'feed_id', 'created_at', 'enabled'])
            ->makeSchema();
    }

}