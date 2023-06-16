<?php

namespace PinaCMS;

use Exception;
use Pina\Container\Container;
use Pina\Container\NotFoundException;
use PinaCMS\SQL\ResourceGateway;

class ResourceTypeFactory
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function register($type, $class)
    {
        $this->container->set($type, $class);
    }

    /**
     * @param $id
     * @return ResourceTypeInterface
     * @throws Exception
     */
    public function make($id): ResourceTypeInterface
    {
        $type = ResourceGateway::instance()->whereId($id)->value('type');
        return $this->makeType($type);
    }

    public function makeType($type): ResourceTypeInterface
    {
        if (!$this->container->has($type)) {
            throw new NotFoundException("Resource type $type not found");
        }

        return $this->container->make($type);
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->container->getKeys();
    }
}