<?php
namespace console\components;
use common\models\Fight;
use console\base\Game;
use Ratchet\ConnectionInterface;

/**
 * FightManager
 */
class FightManager {

	protected $clients = [];
	protected $clientsData = [];
	protected $fights = [];
	protected $router;

	/**
	 * Manager constructor
	 */
	public function __construct() {
		$this->router = new FightRouter($this);
	}

	/**
	 * Add new connection
	 * @deprecated
	 * @param ConnectionInterface $conn
	 */
	public function add(ConnectionInterface $conn) {
		$this->clients[$conn->resourceId] = $conn;
	}

	/**
	 * Add info about connected user
	 * @param ConnectionInterface $conn
	 * @param array $data
	 */
	public function addClient(ConnectionInterface $conn, $data = []) {
		if (isset($this->clients[$conn->resourceId])) {
			unset($this->clients[$conn->resourceId]);
		}
		$this->clientsData[$conn->resourceId] = $data;
	}

	/**
	 * Get user info
	 * @param ConnectionInterface $conn
	 * @return mixed
	 */
	public function getClient(ConnectionInterface $conn) {
		return $this->clientsData[$conn->resourceId];
	}

	/**
	 * Remove client info
	 * @param ConnectionInterface $conn
	 */
	public function remove(ConnectionInterface $conn) {
		if (isset($this->clients[$conn->resourceId])) {
			unset($this->clients[$conn->resourceId]);
		}
		if(isset($this->clientsData[$conn->resourceId])){
			unset($this->clientsData[$conn->resourceId]);
		}
	}

	/**
	 * Get clients
	 * @deprecated
	 * @return array
	 */
	public function getClients() {
		return $this->clients;
	}

	/**
	 * Log new server message
	 * @param $message
	 */
	public function log($message) {
		echo $message.PHP_EOL;
	}

	/**
	 * Process new incoming request
	 * @param ConnectionInterface $from
	 * @param $data
	 */
	public function process(ConnectionInterface $from, $data) {
		$method = implode('', array_map('ucfirst', explode('-', $data->action))).'Request';
		if (method_exists($this->router, $method)) {
			$this->router->{$method}($from, $data);
		}
	}

	/**
	 * Send message to user
	 * @deprecated
	 * @param $id
	 * @param $data
	 */
	public function sendTo($id, $data) {
		if (isset($this->clientsData[$id])) {
			$this->fights[$this->clientsData[$id]['fight']]->getPlayer($this->clientsData[$id]['index'])->send($data);
		}
	}

	/**
	 * Get fight router
	 * @return FightRouter
	 */
	public function getRouter(){
		return $this->router;
	}

	/**
	 * Get game instance
	 * @param $id
	 * @return Game
	 */
	public function getFight($id) {
		if (!isset($this->fights[$id])) {
			$game = new Game();
			$game->setFight(Fight::find()->where('id = :id', [':id' => $id])->one());
			$game->setManager($this);

			$this->fights[$id] = $game;
			unset($game);
		}

		return $this->fights[$id];
	}
}