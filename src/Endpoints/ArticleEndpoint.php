<?php

namespace PinaCMS\Endpoints;

use Pina\Data\DataCollection;
use PinaCMS\Collections\ArticleCollection;
use PinaCMS\Controls\FeedTableView;
use Pina\App;
use Pina\Data\DataTable;
use Pina\Http\DelegatedCollectionEndpoint;

use function Pina\__;

class ArticleEndpoint extends DelegatedCollectionEndpoint
{
    protected function getCollectionTitle(): string
    {
        return __('Статьи');
    }

    protected function makeDataCollection(): DataCollection
    {
        return App::make(ArticleCollection::class);
    }

    protected function makeCollectionView(DataTable $data)
    {
        return App::make(FeedTableView::class)->load($data)->setLocation($this->location(), $this->context()->all());
    }

}