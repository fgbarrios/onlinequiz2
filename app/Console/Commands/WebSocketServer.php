<?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use Ratchet\Server\IoServer;
// use Ratchet\Http\HttpServer;
// use Ratchet\WebSocket\WsServer;
// use App\Http\Controllers\SocketController;
// use React\EventLoop\Factory;
// use React\Socket\Server as ReactSocketServer;
// use React\Socket\SecureServer;

// class WebSocketServer extends Command
// {
//     protected $signature = 'websocket:init';
//     protected $description = 'Start the WebSocket server';

//     public function __construct()
//     {
//         parent::__construct();
//     }

//     public function handle()
//     {
//         $port = config("app.socket_port"); // Change this to your desired port
//         $sslContext = [
//             'local_cert' => '/etc/letsencrypt/live/onlinepoll.trymydemo.com/fullchain.pem',
//             'local_pk' => '/etc/letsencrypt/live/onlinepoll.trymydemo.com/privkey.pem',
//             'verify_peer' => false, // Change to true in production
//         ];

//         $loop = Factory::create();

//         // Create a socket server and add SSL support
//         $socket = new ReactSocketServer("0.0.0.0:$port", $loop);
//         $secureSocket = new SecureServer($socket, $loop, $sslContext);

//         // Create WebSocket server and attach it to the secure socket
//         $webSocketServer = new IoServer(
//             new HttpServer(
//                 new WsServer(
//                     new SocketController() // You can replace this with your WebSocket logic
//                 )
//             ),
//             $secureSocket
//         );

//         $this->info("WebSocket server started on port $port");
//         $loop->run();
//     }
// }

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Ratchet\Server\IoServer;

use Ratchet\Http\HttpServer;

use Ratchet\WebSocket\WsServer;

use React\EventLoop\Factory;

use App\Http\Controllers\SocketController;

class WebSocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new SocketController()
                )
            ),
            config("app.socket_port")
        );
        $server->run();
    }
}