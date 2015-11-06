<?php
namespace console\controllers;

use console\components\ServerProcess;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class ServerController extends \yii\console\Controller
{
    public function actionRun()
    {
        echo 'Trying to start server...'.PHP_EOL;
        $server = IoServer::factory(new HttpServer(new WsServer(new ServerProcess())), 3084);
        $server->run();
        echo 'Server was started successfully. Setup logging to get more details.'.PHP_EOL;
    }
}