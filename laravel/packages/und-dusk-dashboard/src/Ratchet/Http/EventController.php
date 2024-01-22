<?php

namespace Lubianfuchen\DuskDashboard\Ratchet\Http;

use Lubianfuchen\DuskDashboard\Ratchet\Socket;
use Exception;
use GuzzleHttp\Psr7\Response;
use function GuzzleHttp\Psr7\str;
use Psr\Http\Message\RequestInterface;
use Ratchet\ConnectionInterface;
use GuzzleHttp\Psr7\Message;

class EventController extends Controller
{
    public function onOpen(ConnectionInterface $conn, RequestInterface $request = null)
    {
        try {

            /*
             * This is the post payload from our PHPUnit tests.
             * Send it to the connected connections.
             */
            foreach (Socket::$connections as $connection) {
                $connection->send($request->getBody());
            }

            $conn->send(Message::toString(new Response(200)));
        } catch (Exception $e) {
            $conn->send(Message::toString(new Response(500, [], $e->getMessage())));
        }

        $conn->close();
    }
}
