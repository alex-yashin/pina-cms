<?php

namespace PinaCMS\SQL;

use Exception;
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
        $schema->addCreatedAt();
        return $schema;
    }

    public function getTriggers()
    {
        return [
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