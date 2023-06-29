<?php

namespace PinaCMS\Collections;

use PinaCMS\SQL\ResourceGateway;
use Pina\BadRequestException;
use Pina\Data\DataCollection;
use Pina\Data\Schema;

use function Pina\__;

abstract class ResourceBasedCollection extends DataCollection
{

    protected function normalize(array $data, Schema $schema, $context = [], ?string $id = null): array
    {
        if (empty($id) && empty($data['slug']) && !empty($data['title'])) {
            $latinized = substr($this->latinize($data['title']), 0, 24);
            $random = trim(strtolower(base64_encode(rand(0, pow(2, 12)))), '=');
            $data['slug'] = substr($latinized . '-' . $random, 0, 32);
        }

        $slugExists = ResourceGateway::instance()
            ->whereNotId($id)
            ->whereBy('slug', $data['slug'] ?? '')
            ->exists();

        if ($slugExists) {
            $e = new BadRequestException();
            $e->addError(__('Slug exists'), 'slug');
            throw $e;
        }

        return parent::normalize($data, $schema, $context, $id);
    }

    protected function resolveId(array $normalized, Schema $schema, array $context = []): string
    {
        return ResourceGateway::instance()->whereBy('slug', $normalized['slug'])->value('id') ?? '';
    }

    protected function latinize($title)
    {
        $lowercase = array(
            'а',
            'б',
            'в',
            'г',
            'д',
            'е',
            'ё',
            'ж',
            'з',
            'и',
            'й',
            'к',
            'л',
            'м',
            'н',
            'о',
            'п',
            'р',
            'с',
            'т',
            'у',
            'ф',
            'х',
            'ц',
            'ч',
            'ш',
            'щ',
            'ь',
            'ы',
            'ъ',
            'э',
            'ю',
            'я',
            ' '
        );
        $uppercase = array(
            'А',
            'Б',
            'В',
            'Г',
            'Д',
            'Е',
            'Ё',
            'Ж',
            'З',
            'И',
            'Й',
            'К',
            'Л',
            'М',
            'Н',
            'О',
            'П',
            'Р',
            'С',
            'Т',
            'У',
            'Ф',
            'Х',
            'Ц',
            'Ч',
            'Ш',
            'Щ',
            'Ь',
            'Ы',
            'Ъ',
            'Э',
            'Ю',
            'Я',
            ' '
        );
        $en = array(
            'a',
            'b',
            'v',
            'g',
            'd',
            'e',
            'e',
            'zh',
            'z',
            'i',
            'y',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'r',
            's',
            't',
            'u',
            'f',
            'h',
            'ts',
            'ch',
            'sh',
            'sch',
            '',
            'y',
            '',
            'e',
            'yu',
            'ya',
            '-'
        );

        $title = str_replace($lowercase, $en, $title);
        $title = str_replace($uppercase, $en, $title);
        $title = htmlentities($title);

        $title = preg_replace("'&[^;]*;'", "", $title);
        $title = preg_replace("/[^\w]+/", "-", $title);
        $title = preg_replace("/-[-]+/", "-", $title);

        $title = trim($title, '_-');
        return $title;
    }

}