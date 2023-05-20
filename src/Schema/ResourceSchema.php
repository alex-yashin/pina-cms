<?php

namespace PinaCMS\Schema;

use PinaCMS\Types\SlugType;
use Pina\Data\Schema;
use Pina\Types\EnabledType;
use Pina\Types\IntegerType;
use Pina\Types\StringType;

use PinaMedia\Types\MediaType;

use function Pina\__;

class ResourceSchema extends Schema
{

    public function __construct()
    {
        $this->add('id', 'ID', IntegerType::class)->setStatic();
        $this->setPrimaryKey(['id']);

        $this->add('media_id', __("Изображение"), MediaType::class);
        $this->add('title', __("Наименование"), StringType::class);
        $this->add('slug', __("Slug"), SlugType::class);
        $this->add('enabled', __('Активен'), EnabledType::class);
    }

}