<?php

namespace PinaCMS\SQL;

use Exception;
use Pina\Types\TimestampType;
use PinaCMS\Model\Article;
use PinaCMS\Schema\ResourceSchema;
use PinaCMS\Types\FeedType;
use PinaCMS\Types\HTMLType;
use Pina\Data\Schema;
use Pina\DB\ForeignKey;
use Pina\TableDataGateway;

use PinaMedia\MediaGateway;

use function Pina\__;

class ArticleGateway extends TableDataGateway
{

    use ResourceTrait;

    protected static $table = 'article';
    protected static $charset = "utf8mb4";

    /**
     * @return Schema
     * @throws Exception
     */
    public function getSchema()
    {
        $schema = new ResourceSchema();
        $schema->add('feed_id', __("Лента"), FeedType::class)->setNullable();
        $schema->add('text', __("Текст"), HTMLType::class);
        $schema->add('published_at', __("Опубликовать"), TimestampType::class)->setNullable();
        $schema->addCreatedAt();
        return $schema;
    }

    public function getTriggers()
    {
        return [
            [
                $this->getTable(),
                'before insert',
                "IF (NEW.published_at IS NULL) THEN SET NEW.published_at=NOW(); END IF;"
            ],
            $this->makeInsertTrigger('article', 'feed_id'),
            $this->makeUpdateTrigger('feed_id'),
        ];
    }

    public function getForeignKeys()
    {
        return [
            (new ForeignKey('feed_id'))->references(FeedGateway::instance()->getTable(), 'id'),
        ];
    }

    public function firstArticleOrFail(): Article
    {
        $line = $this->selectArticleFields()->firstOrFail();
        return Article::fromArray($line);
    }

    public function findArticleOrFail(string $id): Article
    {
        $line = $this->selectArticleFields()->findOrFail($id);
        return Article::fromArray($line);
    }

    /**
     * @return Article[]
     */
    public function getArticles(): array
    {
        $data = $this->selectArticleFields()->get();

        $r = [];
        foreach ($data as $line) {
            $r[] = Article::fromArray($line);
        }
        return $r;
    }

    public function selectArticleFields()
    {
        return $this->select('text')
            ->select('title')
            ->select('created_at')
            ->select('published_at')
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
            )
            ->leftJoin(
                FeedGateway::instance()->on('id', 'feed_id')
                    ->selectAs('title', 'feed_title')
                    ->leftJoin(
                        ResourceUrlGateway::instance()->alias('feed_url')->on('id', 'id')
                            ->selectAs('url', 'feed_url')
                    )
                    ->leftJoin(
                        MediaGateway::instance()->alias('feed_media')
                            ->on('id', 'media_id')
                            ->selectAs('path', 'feed_media_path')
                            ->selectAs('storage', 'feed_media_storage')
                    )
            );
    }

    public function wherePublished()
    {
        return $this
            ->whereBy('enabled', 'Y')
            ->where($this->getAlias().'.published_at <= NOW()');
    }

}