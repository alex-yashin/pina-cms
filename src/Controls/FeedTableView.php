<?php


namespace PinaCMS\Controls;


use Pina\App;
use Pina\Controls\TableView;
use Pina\Controls\Wrapper;
use Pina\Data\DataRecord;
use Pina\Http\Location;

class FeedTableView extends TableView
{
    /** @var Location */
    protected $context = [];

    protected function drawInner()
    {
        $container = new Wrapper('ul.nav feed');

        foreach ($this->dataTable as $record) {
            /** @var DataRecord $record */
            /** @var FeedRecordRow $row */
            $row = App::make(FeedRecordRow::class);
            $row->load($record);

            $pk = $record->getSinglePrimaryKey($this->context);

            $row->setLink($this->location->link('@/:id', ['id' => $pk ?? 0]));
            $container->append($row);
        }
        return $container;
    }
}