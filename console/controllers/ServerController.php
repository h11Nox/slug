<?php
namespace console\controllers;

use console\components\ServerProcess;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

/**
 * Class ServerController
 * Main background server console command
 * @package console\controllers
 */
class ServerController extends \yii\console\Controller
{
    /**
     * Initialization action
     */
    public function actionRun()
    {
        set_error_handler([$this, 'errorHandler']);
        echo 'Trying to start server...'.PHP_EOL;
        $server = IoServer::factory(new HttpServer(new WsServer(new ServerProcess())), 3084);
        try {
            $server->run();
        } catch (\Exception $e) {
            echo "Catch new process exception: {$e->getMessage()}".PHP_EOL;
        }
        echo 'Server was started successfully. Setup logging to get more details.'.PHP_EOL;
    }

    /**
     * Error handler
     *
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public function errorHandler($errno, $errstr, $errfile, $errline) {
        echo "Catch new error:".PHP_EOL;
        var_dump($errno, $errstr, $errfile, $errline);
    }
}