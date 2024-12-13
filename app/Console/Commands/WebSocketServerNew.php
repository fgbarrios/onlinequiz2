<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Http\Controllers\SocketController;
use React\EventLoop\Factory;
use React\Socket\Server as ReactSocketServer;
use React\Socket\SecureServer;
use Exception;

# nohup php artisan websocket:new > mylog.log 2>&1 & echo $! >> save_pid.txt

class WebSocketServerNew extends Command
{
    protected $signature = 'websocket:new';
    protected $description = 'Start the WebSocket server';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $port = config("app.socket_port"); // Change this to your desired port
            $sslContext = [
                'local_cert' => '/home/centos/fenix24ransim-godaddy-ssl/STAR_fenix24ransim_com.crt',
                'local_pk' => '/home/centos/fenix24ransim-godaddy-ssl/fenix24ransim.com.key',
                'verify_peer' => false, // Change to true in production
            ];

            $loop = Factory::create();

            // Create a socket server and add SSL support
            $socket = new ReactSocketServer("0.0.0.0:$port", $loop);
            $secureSocket = new SecureServer($socket, $loop, $sslContext);

            // Create WebSocket server and attach it to the secure socket
            $webSocketServer = new IoServer(
                new HttpServer(
                    new WsServer(
                        new SocketController() // You can replace this with your WebSocket logic
                    )
                ),
                $secureSocket
            );

            $this->info("WebSocket server started on port $port");
            $loop->run();
        } catch (Exception $e) {
            $this->error("An error occurred: {$e->getMessage()}");
        }
    }
}
