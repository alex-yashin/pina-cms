<?php


namespace PinaCMS\Endpoints;

use PinaCMS\ResourceTypeFactory;
use Pina\Http\Endpoint;

class ResourceEndpoint extends Endpoint
{
    public function show($id)
    {
        return ResourceTypeFactory::make($id)->draw($id);
    }

}