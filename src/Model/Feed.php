<?php


namespace PinaCMS\Model;


use PinaMedia\Model\Media;

class Feed extends Resource
{

    public static function fromArray($line): Feed
    {
        $media = new Media($line['media_storage'] ?? '', $line['media_path'] ?? '');
        return new Feed(
            $line['title'],
            $line['url'],
            $media,
            $line['meta_title'],
            $line['meta_description'],
            $line['meta_keywords']
        );
    }

}