<?php

namespace PinaCMS\Model;

use PinaMedia\Model\Media;

class Article extends Resource
{

    protected $text = '';

    public function __construct(
        string $title,
        string $text,
        string $link,
        Media $media,
        string $metaTitle = '',
        string $metaDescription = '',
        string $metaKeywods = ''
    ) {
        parent::__construct($title, $link, $media, $metaTitle, $metaDescription, $metaKeywods);

        $this->text = $text;
    }

    public function getText()
    {
        return $this->text;
    }

    public static function fromArray($line): Article
    {
        $media = new Media($line['media_storage'] ?? '', $line['media_path'] ?? '');
        return new Article(
            $line['title'],
            $line['text'],
            '/' . $line['url'],
            $media,
            $line['meta_title'],
            $line['meta_description'],
            $line['meta_keywords']
        );
    }

    public function getLabels(): array
    {
        return [];
    }

}