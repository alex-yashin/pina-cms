<?php


namespace PinaCMS\SQL;


use Exception;
use PinaCMS\Types\ResourceType;
use PinaCMS\Types\ResourceTypeType;
use PinaCMS\Types\SlugType;
use Pina\Data\Schema;
use Pina\DB\ForeignKey;
use Pina\SQL;
use Pina\TableDataGateway;
use Pina\Types\EnabledType;
use Pina\Types\IntegerType;
use Pina\Types\StringType;

use PinaMedia\Types\MediaType;

use function Pina\__;

class ResourceGateway extends TableDataGateway
{

    protected static $table = 'resource';

    /**
     * @return Schema
     * @throws Exception
     */
    public function getSchema()
    {
        $schema = new Schema();
        $schema->addAutoincrementPrimaryKey('id', 'ID');
        //будет генерироваться на стороне
        $schema->add('parent_id', __('Родительский ресурс'), ResourceType::class)->setNullable()->setStatic();
        $schema->addKey('parent_id');

        $schema->add('media_id', __("Изображение"), MediaType::class);

        $schema->add('slug', __('Slug'), SlugType::class)->setStatic();
        $schema->addUniqueKey(['slug']);

        //будет генерироваться на стороне
        $schema->add('title', __('Наименование'), StringType::class)->setStatic();
        $schema->addKey('title');//FULLTEXT => $schema->addKey('title', 'FULLTEXT');

        $schema->add('meta_keywords', __('Мета keywords'), StringType::class);
        $schema->add('meta_title', __('Мета title'), StringType::class);
        $schema->add('meta_description', __('Мета description'), StringType::class);

        //задается лишь единожды на стороне и не может быть изменено
        $schema->add('type', __('Тип'), ResourceTypeType::class)->setStatic();

        //будет генерироваться на стороне
        $schema->add('enabled', __('Активен'), EnabledType::class)->setStatic();
        $schema->add('order', __('Порядок'), IntegerType::class);

        return $schema;
    }

    public function getTriggers()
    {
        return [
            [
                $this->getTable(),
                'before insert',
                "SET NEW.slug=IF(NEW.slug IS NULL OR (NEW.slug='' AND NEW.parent_id IS NOT NULL),UUID(),NEW.slug),"
                . "NEW.order=IFNULL(NEW.order, (SELECT IFNULL(MAX(`order`),0)+1 FROM resource));"
            ],
            [
                $this->getTable(),
                'before update',
                "SET NEW.slug=IF(NEW.slug IS NULL OR (NEW.slug='' AND NEW.parent_id IS NOT NULL),UUID(),NEW.slug)"
            ],
        ];
    }

    public function getForeignKeys()
    {
        return [
            (new ForeignKey('parent_id'))->references(ResourceGateway::instance()->getTable(), 'id'),
        ];
    }

    public function withUrl()
    {
        return $this->innerJoin(
            ResourceUrlGateway::instance()->on('id', 'id')->select('url')
        );
    }

    public function withTitlePath()
    {
        $cloned = clone $this;
        $cloned->alias('r')
            ->select('id')
            ->calculate(
                "concat(IFNULL(concat(group_concat(rp.title ORDER BY rt.length DESC SEPARATOR '/'), '/'),''), r.title)",
                'title'
            )
            ->leftJoin(
                ResourceTreeGateway::instance()->alias('rt')->on('id', 'id')
                    ->leftJoin(
                        ResourceGateway::instance()->alias('rp')->on('id', 'parent_id')
                    )
            )
            ->groupBy('r.id');

        return $this->innerJoin(
            SQL::subquery($cloned)->alias('title')->on('id', 'id')
                ->select('title')
        );
    }
}