<?php

namespace PinaCMS\Endpoints;

use Pina\Data\DataCollection;
use PinaCMS\Collections\FeedCollection;
use Pina\App;
use Pina\Http\DelegatedCollectionEndpoint;

use function Pina\__;

class FeedEndpoint extends DelegatedCollectionEndpoint
{

    protected function getCollectionTitle(): string
    {
        return __('Ленты');
    }

    protected function makeDataCollection(): DataCollection
    {
        return App::make(FeedCollection::class);
    }

}