<?php

namespace PinaCMS\SQL;

use Exception;
use PinaCMS\Schema\ResourceSchema;
use Pina\Data\Schema;
use Pina\TableDataGateway;

class FeedGateway extends TableDataGateway
{
    use ResourceTrait;

    protected static $table = 'feed';

    /**
     * @return Schema
     * @throws Exception
     */
    public function getSchema()
    {
        return new ResourceSchema();
    }

    public function getTriggers()
    {
        return [
            $this->makeInsertTrigger('feed'),
            $this->makeUpdateTrigger(),
        ];
    }

}