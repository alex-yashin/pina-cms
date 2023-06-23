<?php

namespace PinaCMS\Model;

use PinaMedia\Model\Media;

class Article extends Resource
{

    protected $text = '';

    /** @var Feed */
    protected $feed;

    public function __construct(
        string $title,
        string $text,
        string $link,
        Media $media,
        Feed $feed,
        string $metaTitle = '',
        string $metaDescription = '',
        string $metaKeywods = ''
    ) {
        parent::__construct($title, $link, $media, $metaTitle, $metaDescription, $metaKeywods);

        $this->text = $text;
        $this->feed = $feed;
    }

    public function getText()
    {
        return $this->text;
    }

    public static function fromArray($line): Article
    {
        $feedMedia = new Media($line['feed_media_storage'] ?? '', $line['feed_media_path'] ?? '');
        $feed = new Feed($line['feed_title'] ?? '', '/' . $line['feed_url'] ?? '', $feedMedia);
        $media = new Media($line['media_storage'] ?? '', $line['media_path'] ?? '');
        return new Article(
            $line['title'],
            $line['text'],
            '/' . $line['url'],
            $media,
            $feed,
            $line['meta_title'],
            $line['meta_description'],
            $line['meta_keywords']
        );
    }

    public function getFeed(): Feed
    {
        return $this->feed;
    }

    public function getLabels(): array
    {
        return [];
    }

}