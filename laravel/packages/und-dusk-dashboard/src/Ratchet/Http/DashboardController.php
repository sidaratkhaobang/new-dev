<?php

namespace Lubianfuchen\DuskDashboard\Ratchet\Http;

use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\str;
use Psr\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;
use GuzzleHttp\Psr7\Message;

class DashboardController extends Controller
{
    public function onOpen(ConnectionInterface $connection, RequestInterface $request = null)
    {
        $connection->send(
            Message::toString(new Response(
                200,
                ['Content-Type' => 'text/html'],
                file_get_contents(__DIR__ . '/../../../resources/views/index.html')
            ))
        );

        $connection->close();
    }
}
