<?php

namespace PinaCMS\SQL;

use PinaCMS\ResourceTypes\FeedResource;
use PinaCMS\Schema\ResourceSchema;
use Pina\Data\Schema;
use Pina\TableDataGateway;

class FeedGateway extends TableDataGateway
{
    use ResourceTrait;

    protected static $table = 'feed';

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getSchema()
    {
        $schema = new ResourceSchema();
        return $schema;
    }

    public function getTriggers()
    {
        $cl = $this->db->escape(FeedResource::class);
        return [
            $this->makeInsertTrigger($cl),
            $this->makeUpdateTrigger(),
        ];
    }

}