<?php
namespace console\components;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * Ratchet Server
 * Class ServerProcess
 * @package console\components
 */
class ServerProcess implements MessageComponentInterface {
    /**
     * Server manager
     * @var FightManager
     */
    protected $manager;

    /**
     * Processor constructor
     */
    public function __construct() {
        $this->manager = new FightManager();
    }

    /**
     * OnOpen event
     * @param ConnectionInterface $conn
     */
    public function onOpen(ConnectionInterface $conn) {
        // $this->manager->add($conn);
    }

    /**
     * OnMessage event
     * @param ConnectionInterface $from
     * @param string $msg
     */
    public function onMessage(ConnectionInterface $from, $msg) {
        $this->manager->process($from, @json_decode($msg));
    }

    /**
     * OnClose event
     * @param ConnectionInterface $conn
     */
    public function onClose(ConnectionInterface $conn) {
        $this->manager->getRouter()->disconnect($conn);
        // $this->manager->remove($conn);
    }

    /**
     * OnError event
     * @param ConnectionInterface $conn
     * @param \Exception $e
     */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $this->manager->getRouter()->error($conn, $e->getMessage());
    }
}