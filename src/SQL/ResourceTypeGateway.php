<?php


namespace PinaCMS\SQL;

use Pina\Data\Schema;
use Pina\TableDataGateway;
use Pina\Types\StringType;

use function Pina\__;

class ResourceTypeGateway extends TableDataGateway
{
    protected static $table = 'resource_type';

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getSchema()
    {
        $schema = new Schema();
        $schema->addAutoincrementPrimaryKey('id', 'ID');
        $schema->add('class', __('Обработчик'), StringType::class);
        return $schema;
    }

    public function selectTitle($alias = 'title')
    {
        return $this->selectAs('class', $alias);
    }
}
