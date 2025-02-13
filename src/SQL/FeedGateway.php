<?php

namespace PinaCMS\SQL;

use Exception;
use PinaCMS\Model\Article;
use PinaCMS\Model\Feed;
use PinaCMS\Schema\ResourceSchema;
use Pina\Data\Schema;
use Pina\TableDataGateway;
use PinaMedia\MediaGateway;

class FeedGateway extends TableDataGateway
{
    use ResourceTrait;

    protected static $table = 'feed';

    /**
     * @return Schema
     * @throws Exception
     */
    public function getSchema()
    {
        $schema = parent::getSchema();
        $schema->merge(new ResourceSchema());
        return $schema;
    }

    public function getTriggers()
    {
        return [
            $this->makeInsertTrigger('feed'),
            $this->makeUpdateTrigger(),
        ];
    }

    public function firstFeedOrFail(): Feed
    {
        $line = $this->selectFeedFields()->firstOrFail();
        return Feed::fromArray($line);
    }

    public function findFeedOrFail(string $id): Feed
    {
        $line = $this->selectFeedFields()->findOrFail($id);
        return Feed::fromArray($line);
    }

    /**
     * @return Article[]
     */
    public function getFeeds(): array
    {
        $data = $this->selectFeedFields()->get();

        $r = [];
        foreach ($data as $line) {
            $r[] = Feed::fromArray($line);
        }
        return $r;
    }

    public function selectFeedFields()
    {
        return $this
            ->select('title')
            ->innerJoin(
                ResourceGateway::instance()->on('id', 'id')
                    ->onBy('enabled', 'Y')
                    ->select('meta_title')
                    ->select('meta_description')
                    ->select('meta_keywords')
            )
            ->leftJoin(
                MediaGateway::instance()
                    ->on('id', 'media_id')
                    ->selectAs('path', 'media_path')
                    ->selectAs('storage', 'media_storage')
            )
            ->innerJoin(
                ResourceUrlGateway::instance()->on('id', 'id')
                    ->select('url')
            );
    }

}