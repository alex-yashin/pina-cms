<?php

namespace PinaCMS\SQL;

use Exception;
use PinaCMS\Schema\ResourceSchema;
use PinaCMS\Types\FeedType;
use PinaCMS\Types\HTMLType;
use Pina\Data\Schema;
use Pina\DB\ForeignKey;
use Pina\TableDataGateway;

use function Pina\__;

class ArticleGateway extends TableDataGateway
{

    use ResourceTrait;

    protected static $table = 'article';
    protected static $charset = "utf8mb4";

    /**
     * @return Schema
     * @throws Exception
     */
    public function getSchema()
    {
        $schema = new ResourceSchema();
        $schema->add('feed_id', __("Лента"),FeedType::class)->setNullable();
        $schema->add('text', __("Текст"),HTMLType::class);
        $schema->addCreatedAt();
        return $schema;
    }

    public function getTriggers()
    {
        return [
            $this->makeInsertTrigger('article', 'feed_id'),
            $this->makeUpdateTrigger('feed_id'),
        ];
    }

    public function getForeignKeys()
    {
        return [
            (new ForeignKey('feed_id'))->references(FeedGateway::instance()->getTable(), 'id'),
        ];
    }

}