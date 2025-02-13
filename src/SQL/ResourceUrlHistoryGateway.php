<?php


namespace PinaCMS\SQL;

use Exception;
use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\IntegerType;
use Pina\Types\LongStringType;

use function Pina\__;

class ResourceUrlHistoryGateway extends TableDataGateway
{
    protected static $table = 'resource_url_history';

    /**
     * @return Schema
     * @throws Exception
     */
    public function getSchema()
    {
        $schema = parent::getSchema();
        $schema->add('id', 'ID',IntegerType::class);
        $schema->add('url', __('Адрес'), LongStringType::class);
        $schema->setPrimaryKey(['url']);
        $schema->addCreatedAt();
        return $schema;
    }


    public function getTriggers()
    {
        $urlTable = ResourceUrlGateway::instance()->getTable();

        return [
            [
                $urlTable,
                'after update',
                "
                    IF (OLD.url <> NEW.url) THEN
                        DELETE FROM resource_url_history WHERE url = OLD.url;
                        INSERT INTO resource_url_history SET id = OLD.id, url = OLD.url;
                    END IF;
                "
            ],
            [
                $urlTable,
                'after delete',
                '
                    IF (OLD.id > 0) THEN
                        DELETE FROM resource_url_history WHERE id = OLD.id;
                    END IF;
                '
            ],
        ];
    }
}