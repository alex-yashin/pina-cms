<?php

namespace PinaCMS\Collections;

use PinaCMS\SQL\FeedGateway;
use Pina\Data\Schema;

class FeedCollection extends ResourceBasedCollection
{
    function makeQuery()
    {
        return FeedGateway::instance();
    }

    public function getListSchema(): Schema
    {
        return parent::getListSchema()
            ->fieldset(['id', 'title'])
            ->makeSchema();
    }

}