<?php


namespace PinaCMS\Controls;


use Pina\App;
use Pina\Controls\TableView;
use Pina\Controls\Wrapper;
use Pina\Data\DataRecord;
use Pina\Data\Schema;
use Pina\Http\Location;

class FeedTableView extends TableView
{
    /** @var Location */
    protected $location;

    protected $context = [];

    public function setLocation(Location $location, array $context)
    {
        $this->location = $location;
        $this->context = $context;
        return $this;
    }

    protected function drawInner()
    {
        $container = new Wrapper('ul.nav feed');

        $pk = $this->calculateContextedPK($this->dataTable->getSchema(), $this->context);
        foreach ($this->dataTable as $record) {
            /** @var DataRecord $record */
            /** @var FeedRecordRow $row */
            $row = App::make(FeedRecordRow::class);
            $row->load($record);

            $row->setLink($this->location->link('@/:id', ['id' => $record->getData()[$pk] ?? 0]));
            $container->append($row);
        }
        return $container;
    }

    /**
     * @param $schema
     * @param $context
     * @return string
     */
    protected function calculateContextedPK(Schema $schema, $context): string
    {
        $primaryKey = $schema->getPrimaryKey();
        foreach ($primaryKey as $k => $pkElement) {
            if (isset($context[$pkElement])) {
                unset($primaryKey[$k]);
            }
        }
        $pk = array_shift($primaryKey);
        if (empty($pk)) {
            $pk = 'id';
        }

        return $pk;
    }
}