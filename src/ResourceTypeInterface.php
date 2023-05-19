<?php


namespace PinaCMS;


use Pina\Controls\Control;
use Pina\Http\Location;

interface ResourceTypeInterface
{
    public function getTitle(): string;

    public function draw(int $id): Control;

    public function getEditLocation(int $id): Location;

}