<?php

namespace PinaCMS;

use PinaCMS\SQL\ResourceGateway;
use PinaCMS\SQL\ResourceTypeGateway;
use Pina\InternalErrorException;

class ResourceTypeFactory
{
    public static function make($id): ResourceTypeInterface
    {
        $class = ResourceTypeGateway::instance()
            ->innerJoin(
                ResourceGateway::instance()->on('type_id', 'id')
                    ->onBy('id', $id)
            )
            ->value('class');

        return static::makeClass($class);
    }

    public static function makeClass($class): ResourceTypeInterface
    {
        if (!class_exists($class) || !is_subclass_of($class, ResourceTypeInterface::class, true)) {
            throw new InternalErrorException();
        }

        /** @var ResourceTypeInterface $inst */
        $inst = new $class;
        return $inst;
    }
}