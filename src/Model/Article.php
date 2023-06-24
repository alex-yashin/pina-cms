<?php

namespace PinaCMS\Model;

use PinaMedia\Model\Media;
use PinaTime\DateTime;

class Article extends Resource
{

    protected $text = '';

    /** @var Feed */
    protected $feed;

    /** @var DateTime */
    protected $publishedAt;

    public function __construct(
        string $title,
        string $text,
        string $link,
        Media $media,
        Feed $feed,
        DateTime $publishedAt,
        string $metaTitle = '',
        string $metaDescription = '',
        string $metaKeywods = ''
    ) {
        parent::__construct($title, $link, $media, $metaTitle, $metaDescription, $metaKeywods);

        $this->text = $text;
        $this->feed = $feed;
        $this->publishedAt = $publishedAt;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
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
            DateTime::createFromServerFormat('Y-m-d H:i:s', $line['published_at'] ?? $line['created_at']),
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