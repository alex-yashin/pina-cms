<?php

namespace PinaCMS\Endpoints;

use PinaCMS\Collections\ArticleCollection;
use PinaCMS\Controls\FeedTableView;
use Pina\App;
use Pina\Data\DataTable;
use Pina\Http\DelegatedCollectionEndpoint;
use Pina\Http\Request;

class ArticleEndpoint extends DelegatedCollectionEndpoint
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->composer->configure('Articles', "Create article");
        $this->collection = App::make(ArticleCollection::class);
    }

    protected function makeCollectionView(DataTable $data)
    {
        if ($this->sortable) {
            return parent::makeCollectionView($data);
        }
        return App::make(FeedTableView::class)->load($data)->setLocation($this->location, $this->context()->all());
    }

}