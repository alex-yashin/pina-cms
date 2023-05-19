<?php

namespace PinaCMS\Collections;

use PinaCMS\SQL\ResourceGateway;
use Pina\BadRequestException;
use Pina\Data\DataCollection;
use Pina\Data\Schema;

use function Pina\__;

abstract class ResourceBasedCollection extends DataCollection
{

    protected function normalize(array $data, Schema $schema, ?string $id = null): array
    {
        $slugExists = ResourceGateway::instance()
            ->whereNotId($id)
            ->whereBy('slug', $data['slug'] ?? '')
            ->exists();

        if ($slugExists) {
            $e = new BadRequestException();
            $e->addError(__('Slug exists'), 'slug');
            throw $e;
        }

        return parent::normalize($data, $schema, $id);

    }

    protected function resolveId(array $normalized, Schema $schema): string
    {
        return ResourceGateway::instance()->whereBy('slug', $normalized['slug'])->value('id') ?? '';
    }

}