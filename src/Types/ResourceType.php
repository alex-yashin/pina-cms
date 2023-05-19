<?php


namespace PinaCMS\Types;


use PinaCMS\SQL\ResourceGateway;
use Pina\TableDataGateway;
use Pina\Types\QueryDirectoryType;
use Pina\Types\ValidateException;

use function Pina\__;

class ResourceType extends QueryDirectoryType
{

    protected function makeQuery(): TableDataGateway
    {
        return ResourceGateway::instance()->withTitlePath();
    }

    public function normalize($value, $isMandatory)
    {
        if (!ResourceGateway::instance()->whereId($value)->exists()) {
            throw new ValidateException(__("Выберите значение"));
        }

        return $value;
    }

}