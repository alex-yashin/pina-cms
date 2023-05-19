<?php

namespace PinaCMS\Endpoints;

use PinaCMS\Collections\FeedCollection;
use Pina\App;
use Pina\Http\DelegatedCollectionEndpoint;
use Pina\Http\Request;

class FeedEndpoint extends DelegatedCollectionEndpoint
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->composer->configure('Feeds', "Create feed");
        $this->collection = App::make(FeedCollection::class);
    }


}