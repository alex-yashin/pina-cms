<?php


namespace PinaCMS\Collections;


use PinaCMS\SQL\ResourceGateway;
use PinaCMS\SQL\ResourceTreeGateway;
use Pina\BadRequestException;
use Pina\Data\DataCollection;
use Pina\Data\Schema;

use function Pina\__;

class ResourceCollection extends DataCollection
{

    protected function makeQuery()
    {
        return ResourceGateway::instance();
    }

    public function getCreationSchema(): Schema
    {
        return new Schema();
    }

    public function getFilterSchema(): Schema
    {
        return parent::getSchema()
            ->fieldset(['parent_id', 'title', 'type_id'])
            ->setNullable()
            ->setStatic(false)
            ->makeSchema();
    }

    public function getListSchema(): Schema
    {
        return parent::getListSchema()->fieldset(['parent_id', 'title', 'slug', 'type_id', 'enabled'])->makeSchema();
    }

    protected function normalize(array $data, Schema $schema, ?string $id = null): array
    {
        if ($id && isset($data['parent_id']) && $data['parent_id'] == $id) {
            $ex = new BadRequestException();
            $ex->setErrors([[__("Родительский ресурс должен отличаться от текущего"), 'parent_id']]);
            throw $ex;
        }

        if ($id && isset($data['parent_id'])) {
            $isChild = ResourceTreeGateway::instance()
                ->whereBy('parent_id', $id)
                ->whereBy('id', $data['parent_id'])
                ->exists();
            if ($isChild) {
                $ex = new BadRequestException();
                $ex->setErrors([[__("Родительский ресурс не должен быть вложенным в текущий"), 'parent_id']]);
                throw $ex;
            }
        }

        return parent::normalize($data, $schema, $id);
    }


}