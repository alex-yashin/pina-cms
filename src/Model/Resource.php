<?php


namespace PinaCMS\Model;


use Pina\Model\LinkedItem;
use PinaMedia\Model\Media;

class Resource extends LinkedItem
{
    /** @var Media */
    protected $media;

    protected $metaTitle = '';
    protected $metaDescription = '';
    protected $metaKeywords = '';

    public function __construct(
        string $title,
        string $link,
        Media $media,
        string $metaTitle = '',
        string $metaDescription = '',
        string $metaKeywods = ''
    ) {
        parent::__construct($title, $link);

        $this->media = $media;

        $this->metaTitle = $metaTitle;
        $this->metaDescription = $metaDescription;
        $this->metaKeywords = $metaKeywods;
    }

    public function getMedia(): Media
    {
        return $this->media;
    }

    public function getMetaTitle(): string
    {
        return $this->metaTitle ? $this->metaTitle : $this->title;
    }

    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    public function getMetaKeywords(): string
    {
        return $this->metaKeywords;
    }
}