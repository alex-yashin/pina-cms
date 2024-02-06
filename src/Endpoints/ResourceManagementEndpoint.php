<?php


namespace PinaCMS\Endpoints;


use Pina\Data\DataRecord;
use PinaCMS\Collections\ResourceCollection;
use Pina\App;
use Pina\Http\DelegatedCollectionEndpoint;
use Pina\Http\Request;
use Pina\Url;

use function Pina\__;

class ResourceManagementEndpoint extends DelegatedCollectionEndpoint
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->composer->configure(__('Ресурсы'), '');
        $this->collection = App::make(ResourceCollection::class);
    }

    protected function makeViewButtonRow(DataRecord $record)
    {
        $row = parent::makeViewButtonRow($record);

        $resource = $this->location->resource('@');
        list($controller, $action, $data) = Url::route($resource, 'get');

        $id = $data['id'] ?? 0;

//        $type = ResourceGateway::instance()
//            ->whereId($id)
//            ->value('type_id');

        $row->append($this->makeLinkedButton('View data', $this->location->link('')));

        return $row;
    }

}