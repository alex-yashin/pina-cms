<?php

namespace PinaCMS\Composers;

use Pina\App;
use Pina\Controls\BreadcrumbView;
use Pina\Controls\Meta;
use Pina\Data\DataTable;
use Pina\Data\Schema;
use PinaCMS\Model\Resource;
use PinaCMS\SQL\ResourceGateway;
use PinaCMS\SQL\ResourceTreeGateway;
use PinaCMS\SQL\ResourceUrlGateway;

class MetaComposer
{

    /**
     * @param string $ogType
     * @param Resource $resource
     * @throws \Exception
     */
    public function set(string $ogType, Resource $resource, $id = null): Meta
    {
        App::place('page_header')->set($resource->getTitle());
        App::place('meta_title')->set($resource->getMetaTitle());
        App::place('meta_description')->set($resource->getMetaDescription());
        App::place('meta_keywords')->set($resource->getMetaKeywords());

        App::place('canonical')->set($resource->getLink());

        if ($id) {
            $this->fillBreadcrumbs($id, $resource->getTitle());
        }

        /** @var Meta $meta */
        $meta = App::load(Meta::class);
        $meta->set('title', $resource->getMetaTitle());
        $meta->set('description', $resource->getMetaDescription());
        $meta->set('keywords', $resource->getMetaKeywords());
        $meta->set('og:type', $ogType);
        $meta->set('og:title', $resource->getMetaTitle());
        $meta->set('og:description', $resource->getMetaDescription());
        $meta->set('og:url', App::link($resource->getLink()));
        $meta->set('og:image', $resource->getMedia()->getUrl());
        return $meta;
    }

    protected function fillBreadcrumbs($id, $title)
    {
        $links = ResourceUrlGateway::instance()
            ->selectAs('url', 'link')
            ->calculate('true', 'is_active')
            ->innerJoin(
                ResourceGateway::instance()->on('id', 'id')
                    ->select('title')
            )
            ->innerJoin(
                ResourceTreeGateway::instance()
                    ->on('parent_id', 'id')
                    ->onBy('id', $id)
            )
            ->get();

        $links[] = [
            'link' => '',
            'title' => $title,
            'is_active' => false,
        ];

        foreach ($links as $k => $v) {
            if (!empty($v['link'])) {
                $links[$k]['link'] = App::link($v['link']);
            }
        }

        $view = App::make(BreadcrumbView::class);
        $view->load(new DataTable($links, new Schema()));

        App::place('breadcrumb')->set($view);

    }

}