<?php


namespace PinaCMS;


use PinaCMS\SQL\ResourceUrlGateway;
use PinaCMS\SQL\ResourceUrlHistoryGateway;

class Dispatcher
{

    public function dispatch($resource)
    {
        $resource = trim(urldecode($resource), '/');
        if (empty($resource)) {
            return null;
        }

        $id = ResourceUrlGateway::instance()
            ->whereBy('url', $resource)
            ->value('id');

        if (!empty($id)) {
            return 'rs/'.$id;
        }

        $redirect = ResourceUrlGateway::instance()
            ->innerJoin(
                ResourceUrlHistoryGateway::instance()->on('id')->whereBy('url', $resource)
            )
            ->value('url');

        if (!empty($redirect)) {
            header('HTTP/1.1 301 Moved Permanently');
            header("Location: /" . $redirect);
            exit;
        }

        return null;
    }

}
