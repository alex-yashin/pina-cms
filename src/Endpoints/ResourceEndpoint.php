<?php


namespace PinaCMS\Endpoints;

use Exception;
use Pina\App;
use Pina\Controls\Control;
use PinaCMS\ResourceTypeFactory;
use Pina\Http\Endpoint;

class ResourceEndpoint extends Endpoint
{
    /**
     * @param $id
     * @return Control
     * @throws Exception
     */
    public function show($id)
    {
        /** @var ResourceTypeFactory $factory */
        $factory = App::load(ResourceTypeFactory::class);
        return $factory->make($id)->draw($id);
    }

}