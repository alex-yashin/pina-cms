<?php


namespace PinaCMS\Endpoints;

use Pina\App;
use PinaCMS\ResourceTypeFactory;
use Pina\Http\Endpoint;

class ResourceEndpoint extends Endpoint
{
    /**
     * @param $id
     * @return \Pina\Controls\Control
     * @throws \Exception
     */
    public function show($id)
    {
        /** @var ResourceTypeFactory $factory */
        $factory = App::load(ResourceTypeFactory::class);
        return $factory->make($id)->draw($id);
    }

}