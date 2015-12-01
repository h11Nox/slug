<?php
namespace console\components;
use common\models\Fight;
use common\models\FightUser;
use common\models\User;
use console\base\GameException;
use Ratchet\ConnectionInterface;

/**
 * FightRouter class
 * Provides user requests routing
 */
class FightRouter {

	/**
	 * FightManager Instance
	 * @var FightManager
	 */
	protected $manager;

	/**
	 * Constructor
	 * @param FightManager $fm
	 */
	public function __construct(FightManager $fm) {
		$this->manager = $fm;
	}

	/**
	 * User connection request
	 * Will be triggered when the user open the fight page
	 * @param ConnectionInterface $from
	 * @param $data
	 */
	public function connectRequest(ConnectionInterface $from, $data) {
		$fight = $this->manager->getFight($data->fight);
		$user = FightUser::find()->where('fight_id = :id AND user_id = :user', [
			':id' => $data->fight,
			':user' => $data->user
		])->one();
		// Add connected player
		$p = $fight->getPlayer($data->index);
		$isReconnect = !$p->isNew();
		if (!$isReconnect) {
			$fight->addPlayer($data->index, $from, $user, $data->index);
		} else {
			$p->connect($from);
		}

		// Set user data to manager. Will be used for disconnecting
		$this->manager->addClient($from, ['user' => $data->user, 'fight' => $data->fight, 'index' => $data->index]);

		// Log user connection event
		$this->manager->log("User ({$from->resourceId}) has been ".(!$isReconnect ? 'connected' : 'reconnected')." to fight {$data->fight}");

		// Send message about connecting
		$player = $fight->getPlayer($data->index);
		foreach ($fight->getPlayers() as $p) {
			$p->send([
				'action' => 'message',
				'text' => $player->getName().' присоединился к игре'
			]);
		}

		if (!$player->isOwner() && !$isReconnect) {
			$fight->start();
			$fight->getOpponent($data->index)->send([
				'action' => 'connect',
				'id' => $data->user
			]);
		}

		if ($isReconnect && $fight->isFull()) {
			// echo 'Sending reconnect request '.PHP_EOL;
			$player->send([
				'action' => 'reconnect',
				'active' => 1,
				'ids' => [
					1 => $fight->getPlayer1()->getFightUser()->user_id,
					2 => $fight->getPlayer2()->getFightUser()->user_id,
				],
				'data' => $this->getResponseData($fight)
			]);
		}
	}

	/**
	 * Disconnect user from game
	 * @param ConnectionInterface $from
	 */
	public function disconnect(ConnectionInterface $from) {
		// Log user disconnection event
		$this->manager->log("User ({$from->resourceId}) has been disconnected from fight");

		// Get user data
		$data = $this->manager->getClient($from);
		// Get disconnected player
		$player = $this->manager->getFight($data['fight'])->getPlayer($data['index']);

		$player->getOpponent()->send([
			'action' => 'message',
			'text' => $player->getName().' отсоединился от игры'
		]);

		$player->disconnect();
	}

	/**
	 * Error handling
	 * @param ConnectionInterface $conn
	 * @param $message
	 */
	public function error(ConnectionInterface $conn, $message) {
		// @todo
	}

	/**
	 * Message Request
	 * Is triggered when a user send a message
	 * @param ConnectionInterface $from
	 * @param $data
	 */
	public function messageRequest(ConnectionInterface $from, $data) {
		$fight = $this->manager->getFight($data->fight);
		foreach ($fight->getPlayers() as $player) {
			$player->send([
				'action' => 'message',
				'text' => $player->getName().': '.$data->text
			]);
		}
	}

	/**
	* Init Request
	* Initialize a game. Send some cards for start
	* @param ConnectionInterface $from
	* @param $data
	*/
	public function initRequest(ConnectionInterface $from, $data) {
		$fight = $this->manager->getFight($data->fight);
		if (!$fight->isInitialized()) {
			$fight->initFight();
			if ($fight->isNew()) {
				$this->manager->log('Starting new fight');
				$fight->start();
			}
			$data = $this->getResponseData($fight);

			foreach ($fight->getPlayers() as $player) {
				$player->send([
					'action' => 'init',
					'active' => 1,
					'move' => 1,
					'data' => $data
				]);
			}

			unset($data);
		}
		unset($fight);
	}

	/**
	* Use user card
	* @param ConnectionInterface $conn
	* @param $data
	*/
	public function useRequest(ConnectionInterface $conn, $data) {
		$this->manager->log('User used a card');
		$fight = $this->manager->getFight($data->fight);
		$fight->getPlayer($data->index)->useCard($data->card);
	}

	/**
	 * Ends selected user turn
	 * @param ConnectionInterface $conn
	 * @param $data
	 * @throws GameException When trying end not active player turn
	 */
	public function endTurnRequest(ConnectionInterface $conn, $data) {
		$this->manager->log('User ended his turn');
		$fight = $this->manager->getFight($data->fight);
		$fight->endTurn((int)$data->player);
	}

	/**
	 * Get response players data
	 * @param $fight
	 * @return array
	 */
	protected function getResponseData($fight) {
		return [
			1 => $fight->getPlayer1()->getResponse(),
			2 => $fight->getPlayer2()->getResponse(),
		];
	}
}