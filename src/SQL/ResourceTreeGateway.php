<?php


namespace PinaCMS\SQL;

use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\EnabledType;
use Pina\Types\IntegerType;

use function Pina\__;

class ResourceTreeGateway extends TableDataGateway
{
    protected static $table = 'resource_tree';

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getSchema()
    {
        $schema = new Schema();
        $schema->add('id', 'ID', IntegerType::class);
        $schema->add('parent_id', __('Родительский ресурс'), IntegerType::class);
        $schema->setPrimaryKey(['id', 'parent_id']);

        $schema->add('length', __('Глубина'), IntegerType::class);

        //денормализованные поля
        $schema->add('enabled', __('Активен'), EnabledType::class);
        $schema->add('order', __('Порядок'), IntegerType::class);

        $schema->addKey(['parent_id', 'length', 'enabled', 'order']);
        return $schema;
    }


    public function getTriggers()
    {
        $addTreeNode = "
            IF (NEW.parent_id IS NOT NULL) THEN
                INSERT INTO resource_tree (parent_id, id, length, enabled, `order`)
                SELECT resource_tree.parent_id, NEW.id, resource_tree.length + 1, NEW.enabled, NEW.`order`
                FROM resource_tree WHERE id = NEW.parent_id
                UNION
                SELECT NEW.parent_id, NEW.id, 1, NEW.enabled, NEW.`order`;
            END IF;";


        $resourceTable = ResourceGateway::instance()->getTable();

        return [
            [
                $resourceTable,
                'after insert',
                $addTreeNode,
            ],
            [
                $resourceTable,
                'after update',
                "
                    IF (OLD.parent_id <> NEW.parent_id OR (OLD.parent_id IS NULL AND NEW.parent_id IS NOT NULL) OR (OLD.parent_id IS NOT NULL AND NEW.parent_id IS NULL)) THEN
                        DELETE `t1` FROM resource_tree as t1
                        JOIN resource_tree t2
                            ON t1.id = t2.id 
                            AND t2.parent_id = OLD.id
                        JOIN resource_tree t3 
                            ON t1.parent_id = t3.parent_id
                            AND t3.id = OLD.id;
                        DELETE FROM resource_tree WHERE id = OLD.id;

                        $addTreeNode

                        INSERT INTO resource_tree (parent_id, id, length, enabled, `order`)
                        SELECT t1.parent_id, t2.id, t1.length + t2.length, t2.enabled, t2.`order`
                        FROM resource_tree t1 CROSS JOIN resource_tree t2
                        WHERE t1.id = NEW.id AND t2.parent_id = NEW.id;
                        
                    ELSEIF (OLD.enabled <> NEW.enabled OR OLD.`order` <> NEW.`order`) THEN
                            UPDATE resource_tree SET enabled = NEW.enabled, `order`=NEW.`order` WHERE id = NEW.id;
                    END IF;
                "
            ],
            [
                $resourceTable,
                'after delete',
                '
                    DELETE t1 FROM resource_tree t1
                    JOIN resource_tree t2
                        ON t1.id = t2.id 
                        AND t2.parent_id = OLD.id
                    JOIN resource_tree t3 
                        ON t1.parent_id = t3.parent_id
                        AND t3.id = OLD.id;
                    DELETE FROM resource_tree WHERE id = OLD.id;
                    DELETE FROM resource_tree WHERE parent_id = OLD.id;
                '
            ],
        ];
    }


    public function findChildIds($id, $level = 0)
    {
        if (is_array($id)) {
            $id = array_map('intval', $id);
        } else {
            $id = intval($id);
            if ($id == 0) {
                return false;
            }
        }

        return $this->whereBy('parent_id', $id)->whereLevel($level)->column('id');
    }

    public function whereLevel($level = 0)
    {
        $level = intval($level);
        if (empty($level)) {
            return $this;
        }

        return $this->whereBy('length', $level);
    }

}