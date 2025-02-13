<?php


namespace PinaCMS\SQL;

use Exception;
use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\IntegerType;
use Pina\Types\LongStringType;

use function Pina\__;

class ResourceUrlGateway extends TableDataGateway
{

    protected static $table = 'resource_url';

    /**
     * @return Schema
     * @throws Exception
     */
    public function getSchema()
    {
        $schema = parent::getSchema();
        $schema->add('id', 'ID',IntegerType::class);
        $schema->setPrimaryKey(['id']);

        $schema->add('url', __("Адрес"),LongStringType::class);
        $schema->addUniqueKey(['url']);
        return $schema;
    }

    public function getTriggers()
    {
        $resourceTable = ResourceGateway::instance()->getTable();

        return [
            [
                $resourceTable,
                'after insert',
                "
                    INSERT INTO resource_url SET id = NEW.id, url = (
                        SELECT TRIM(BOTH '/' FROM concat(IFNULL(concat(group_concat(rp.slug ORDER BY rt.length DESC SEPARATOR '/'), '/'),''), r.slug))
                            from resource r 
                            left join resource_tree rt on rt.id = r.id
                            left join resource rp on rp.id = rt.parent_id
                            WHERE r.id = NEW.id
                            GROUP BY r.id
                            LIMIT 1
                    );
                ",
            ],
            [
                $resourceTable,
                'after update',
                "
                    IF (OLD.slug <> NEW.slug OR OLD.parent_id <> NEW.parent_id OR (OLD.parent_id IS NULL AND NEW.parent_id IS NOT NULL) OR (OLD.parent_id IS NOT NULL AND NEW.parent_id IS NULL)) THEN
                        UPDATE resource_url ru
                        inner join (
                            SELECT id FROM resource_tree WHERE parent_id = NEW.id 
                            UNION 
                            select NEW.id as id
                        ) as rt_link on rt_link.id = ru.id
                        inner join (
                            SELECT r.id, TRIM(BOTH '/' FROM concat(IFNULL(concat(group_concat(rp.slug ORDER BY rt.length DESC SEPARATOR '/'), '/'),''), r.slug)) as url
                            from resource r
                            inner join (SELECT resource_tree.id FROM resource_tree WHERE resource_tree.parent_id = NEW.id UNION select NEW.id as id) as rt_link on rt_link.id = r.id
                            left join resource_tree rt on rt.id = r.id
                            left join resource rp on rp.id = rt.parent_id
                            GROUP BY r.id
                        ) d on d.id = ru.id
                        SET ru.url = d.url;

                    END IF;
                "
            ],
            [
                $resourceTable,
                'after delete',
                'DELETE FROM resource_url WHERE id = OLD.id;'
            ],
        ];
    }
}
