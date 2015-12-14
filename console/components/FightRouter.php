<?php
namespace console\components;

use common\models\Fight;
use common\models\FightUser;
use common\models\User;
use console\base\GameException;
use console\base\http\Response;
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
			$response = (new Response())
				->setAction('message')
				->setText($player->getName().' присоединился к игре');
			$p->send($response);
		}

		if (!$player->isOwner() && !$isReconnect) {
			$fight->start();
			$response = (new Response())
				->setAction('connect')
				->setId($data->user);
			$fight->getOpponent($data->index)->send($response);
		}

		if ($isReconnect && $fight->isFull()) {
			$fight->getPlayer(1)->startTimer();
			$response = (new Response())
				->setAction('reconnect')
				->setPlayers($fight->getPlayers())
				->setFight($fight)
				->setIds(true);
			$player->send($response);
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

		$response = (new Response())
			->setAction('message')
			->setText($player->getName().' отсоединился от игры');
		$player->getOpponent()->send($response);

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
			$response = (new Response())
				->setAction('message')
				->setPlayer($fight->getPlayer($data->index))
				->setText($player->getName().': '.$data->text);
			$player->send($response);
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
			$response = (new Response())
				->setAction('init')
				->setPlayers($fight->getPlayers())
				->setFight($fight);
			$fight->send($response);
		}
	}

	/**
	* Use user card
	* @param ConnectionInterface $conn
	* @param $data
	*/
	public function useRequest(ConnectionInterface $conn, $data) {
		$this->manager->log('User used a card');
		$this->manager
			->getFight($data->fight)
			->getPlayer($data->index)
			->useCard($data->card, $data->data);
	}

	/**
	 * Ends selected user turn
	 * @param ConnectionInterface $conn
	 * @param $data
	 * @throws GameException When trying end not active player turn
	 */
	public function endTurnRequest(ConnectionInterface $conn, $data) {
		$this->manager->log('User ended his turn');
		$this->manager
			->getFight($data->fight)
			->endTurn((int)$data->player);
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