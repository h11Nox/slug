<?php
namespace console\base;
use common\base\GameSettings;
use common\models\Fight;
use console\base\http\Response;

/**
 * Game Class
 */
class Game extends \common\base\Game {

	protected $manager;
	protected $fight;
	protected $phase;
	protected $initialized = false;

	/**
	 * Initialization function
	 */
	protected function initialize() {
		$this->_settings = new GameSettings();
		$this->_player1 = new Player();
		$this->_player2 = new Player();
		$this->phase = new GamePhase();

		$this->initEvents();
	}

	/**
	 * Get game phase
	 * @return GamePhase
	 */
	public function getPhase() {
		return $this->phase;
	}

	/**
	 * Init fight event
	 */
	protected function initEvents() {
		$this->on('player-use-card', [$this, 'useCard']);
		$this->on('user-message', [$this, 'userMessage']);
	}

	/**
	 * Set process manager
	 * @param $manager
	 */
	public function setManager($manager) {
		$this->manager = $manager;
	}

	/**
	 * Get process manager
	 * @return mixed
	 */
	public function getManager() {
		return $this->manager;
	}

	/**
	 * Set Fight
	 * @param Fight $fight
	 */
	public function setFight(Fight $fight) {
		$this->fight = $fight;

		$this->start();
	}

	/**
	 * Starting the fight
	 */
	public function start() {
		if($this->isNew()){
			$this->fight->start();
		}
	}

	/**
	 * If it's new fight
	 * @return mixed
	 */
	public function isNew(){
		return $this->fight->isNew();
	}

	/**
	 * Set Player1 object
	 * @param $player
	 */
	public function setPlayer1($player) {
		$this->_player1 = $player;
		$this->_player1->setGame($this);
	}

	/**
	 * Set Player2 object
	 * @param $player
	 */
	public function setPlayer2($player) {
		$this->_player2 = $player;
		$this->_player2->setGame($this);
	}

	/**
	 * Add new player to fight
	 * @param $index
	 * @param $conn
	 * @param $user
	 * @param int $index
	 */
	public function addPlayer($index, $conn, $user, $index = 1) {
		$owner = $index == 1;
		// @todo - REFACTOR THIS!!!
		$player = new Player();
		$player->setOwner($owner);
		$player->connect($conn);
		$player->setGameUser($user);
		$player->setIndex($index);
		$player->setIsNew(false);
		$player->initCards();
		$player->addCards(3);
		$player->setActive($index == 1);

		$this->{'setPlayer'.($owner ? 1 : 2)}($player);
	}

	/**
	 * Get players ids
	 * @return array
	 */
	public function getIds() {
		$data = [];
		foreach($this->getPlayers() as $player) {
			// @todo
			$data[] = 1;
		}

		return $data;
	}

	/**
	 * Get player by index
	 * @param $index
	 * @return mixed
	 */
	public function getPlayer($index) {
		return $this->{'getPlayer'.$index}();
	}

	/**
	 * Get player opponent
	 * @param $index
	 * @return mixed
	 */
	public function getOpponent($index) {
		return $this->getPlayer($index == 1 ? 2 : 1);
	}

	/**
	 * Get fight players
	 * @return array
	 */
	public function getPlayers() {
		$data = [];
		for ($i=1; $i<=2; $i++) {
			$player = $this->{'getPlayer'.$i}();
			if (is_object($player) && $player->isConnected()) {
				$data[] = $player;
			}
		}

		return $data;
	}

	/**
	 * If fight is full
	 * @return bool
	 */
	public function isFull() {
		return count($this->getPlayers()) === 2;
	}

	/**
	 * Init fight
	 */
	public function initFight() {
		$this->initialized = true;
	}

	/**
	 * @return bool
	 */
	public function isInitialized() {
		return $this->initialized;
	}

	/**
	 * End user turn
	 * @param $index
	 * @throws GameException
	 */
	public function endTurn($index) {
		$player = $this->getPlayer($this->phase->getPlayer());
		if ($player->getIndex() !== $index) {
			throw new GameException('User is not active already');
		}
		$player->setActive(false)->stopTimer();
		$this->getOpponent($player->getIndex())->setActive(true)->startTimer();

		$this->phase->end();
		$response = (new Response())
			->setAction('endTurn')
			->setPlayer($player);

		array_map(function($p){
			$p->getData()->afterTurn();
		}, $this->getPlayers());

		$this->send($response);
	}

	/**
	 * Use card
	 * @param $event
	 */
	protected function useCard($event) {
		$response = (new Response())
			->setAction('use')
			->setCard($event->card)
			->setPlayer($event->player)
			->setFight($this);
		$this->send($response);
	}

	/**
	 * Set response
	 * @param Response $response
	 */
	public function send(Response $response) {
		$response->setFight($this);
		foreach ($this->getPlayers() as $p) {
			$p->send($response);
		}
	}

	/**
	 * User message
	 * @param $event
	 */
	protected function userMessage($event) {
		$cm = \Yii::$app->getFormatter()->asShortSize(memory_get_usage(true));
		$pm = \Yii::$app->getFormatter()->asShortSize(memory_get_peak_usage(true));
		$this->manager->log("Current memory: {$cm}; peak memory: {$pm}");
	}
}

class GameException extends \Exception{}