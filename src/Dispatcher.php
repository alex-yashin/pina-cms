<?php


namespace PinaCMS;


use Pina\Router\DispatcherInterface;
use PinaCMS\SQL\ResourceUrlGateway;
use PinaCMS\SQL\ResourceUrlHistoryGateway;

class Dispatcher implements DispatcherInterface
{

    public function dispatch(string $resource): ?string
    {
        $resource = trim(urldecode($resource), '/');
        $id = ResourceUrlGateway::instance()
            ->whereBy('url', $resource)
            ->value('id');

        $_SERVER['PINA_CMS_RESOURCE_ID'] = $id;

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
