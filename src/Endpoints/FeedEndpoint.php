<?php

namespace PinaCMS\Endpoints;

use PinaCMS\Collections\FeedCollection;
use Pina\App;
use Pina\Http\DelegatedCollectionEndpoint;
use Pina\Http\Request;

use function Pina\__;

class FeedEndpoint extends DelegatedCollectionEndpoint
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->composer->configure(__('Ленты'), __('Добавить ленту'));
        $this->collection = App::make(FeedCollection::class);
    }


}