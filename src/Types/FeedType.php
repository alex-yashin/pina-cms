<?php

namespace PinaCMS\Types;

use PinaCMS\SQL\FeedGateway;
use Pina\TableDataGateway;
use Pina\Types\QueryDirectoryType;
use Pina\Types\ValidateException;

use function Pina\__;

class FeedType extends QueryDirectoryType
{

    protected function makeQuery(): TableDataGateway
    {
        return FeedGateway::instance();
    }

    public function normalize($value, $isMandatory)
    {
        if (!FeedGateway::instance()->whereId($value)->exists()) {
            throw new ValidateException(__("Выберите значение"));
        }

        return $value;
    }

}