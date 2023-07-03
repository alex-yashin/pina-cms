<?php

namespace PinaCMS\Types;

use Pina\App;
use Pina\Html;
use Pina\Types\StringType;
use PinaCMS\SQL\ResourceGateway;
use PinaCMS\SQL\ResourceUrlGateway;

class SlugType extends StringType//UUIDType
{
    public function getSize(): int
    {
        return 40;//TODO: сократить до размера UUID 36
    }

    public function draw($value): string
    {
        $url = ResourceUrlGateway::instance()
            ->innerJoin(
                ResourceGateway::instance()
                    ->on('id', 'id')
                    ->onBy('slug', $value)
            )
            ->value('url');
        return Html::a($value, $url ? App::link($url) : '#', ['target' => '_blank']);

    }
}