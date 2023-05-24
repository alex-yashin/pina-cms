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

    public function getListSchema(): Schema
    {
        return parent::getListSchema()
            ->fieldset(['title', 'text', 'feed_id', 'created_at', 'enabled'])
            ->makeSchema();
    }

    public function getFilterSchema(): Schema
    {
        return $this->getListSchema()->forgetField('text')->forgetStatic()->setNullable()->setMandatory(false);
    }

}