<?php


namespace PinaCMS\Endpoints;


use Pina\Controls\ButtonRow;
use Pina\Data\DataCollection;
use Pina\Data\DataRecord;
use PinaCMS\Collections\ResourceCollection;
use Pina\App;
use Pina\Http\DelegatedCollectionEndpoint;
use Pina\Url;

use function Pina\__;

class ResourceManagementEndpoint extends DelegatedCollectionEndpoint
{
    protected function getCollectionTitle(): string
    {
        return __('Ресурсы');
    }

    protected function makeDataCollection(): DataCollection
    {
        return App::make(ResourceCollection::class);
    }

    protected function makeViewButtonRow(DataRecord $record): ButtonRow
    {
        $row = parent::makeViewButtonRow($record);

        $resource = $this->location()->resource('@');
        list($controller, $action, $data) = Url::route($resource, 'get');

        $id = $data['id'] ?? 0;

//        $type = ResourceGateway::instance()
//            ->whereId($id)
//            ->value('type_id');

        $row->append($this->makeLinkedButton('View data', $this->location()->link('')));

        return $row;
    }

}