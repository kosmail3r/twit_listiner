<?php
namespace Kosmailer\TwitQuarterFeeder;
require_once __DIR__ . "/../vendor/autoload.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Ukrbublik\TestFeed\App;
use Ratchet\Session\SessionProvider;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;
use React\Socket\Server as Reactor;
use React\EventLoop\Factory as LoopFactory;
use React\EventLoop\LoopInterface;

$app = new App();

$loop = LoopFactory::create();

$rapp = new RatchetApp($loop, $app->config);
$srapp = new SessionProvider(
    $rapp,
    new MemcachedSessionHandler($app->memcached)
);
$rapp->setSessionProvider($srapp);
$component = new HttpServer(new WsServer($srapp));
$socket = new Reactor($loop);
$socket->listen($app->config['ratchet']['listenPort'], '0.0.0.0');
echo "Listening on " . $app->config['ratchet']['listenPort'] . "...\n";
$server = new IoServer($component, $socket, $loop);

$loop->run();
?>
