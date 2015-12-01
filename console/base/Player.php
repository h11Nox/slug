<?php
namespace console\base;
use backend\models\User;
use console\base\events\UseCardEvent;
use console\base\events\UserSendEvent;
use Ratchet\ConnectionInterface;

/**
 * Player Class
 */
class Player extends \common\base\Player {

	protected $index;
	protected $game;
	protected $connected = false;
	protected $new = true;
	protected $connection;
	protected $owner = false;
	protected $cards;
	protected $active = false;

	/**
	 * Constructor
	 */
	public function init(){
		$this->data = $this->getPlayerData();
	}

	/**
	 * Get user index
	 * @return mixed
	 */
	public function getIndex() {
		return $this->index;
	}

	/**
	 * @return PlayerData
	 */
	protected function getPlayerData() {
		return new PlayerData();
	}

	/**
	 * Get Cards
	 * @return CardFactory
	 */
	protected function getCards() {
		if (is_null($this->cards)) {
		   $this->cards = new CardFactory();
		}

		return $this->cards;
	}

	/**
	 * Get user response data
	 * @return mixed
	 */
	public function getResponse(){
		return $this->data->getResponse();
	}

	/**
	 * @param $number
	 */
	public function addCards($number) {
		$this->data->addCards($this->getCards()->get($number));
	}

	/**
	 * Init Card;
	 */
	public function initCards() {
		$this->getCards()->setIds($this->_user->cards_list);
	}

	/**
	 * Use the card
	 * @param $cardID
	 */
	public function useCard($cardID) {
		$card = $this->data->getCard($cardID);
		$card->setIndex($cardID);
		if ($card->cost <= $this->data->getPoints()) {
			// Use mp
			$this->data->useCard($cardID);

			// Trigger card usage event
			$event = new UseCardEvent();
			$event->card = $card;
			$event->player = $this;
			$this->game->trigger('player-use-card', $event);
		}
	}

	/**
	 * Set game
	 * @param $game
	 */
	public function setGame($game) {
		$this->game = $game;
	}

	/**
	 * Connect
	 * @param ConnectionInterface $conn
	 */
	public function connect(ConnectionInterface $conn) {
		$this->connected = true;
		$this->connection = $conn;
	}

	/**
	 * Disconnect
	 */
	public function disconnect() {
		$this->connected = false;
		$this->connection = null;
	}

	/**
	 * Get FightUser
	 * @return mixed
	 */
	public function getFightUser() {
		return $this->_user;
	}

	/**
	 * Is connected
	 * @return bool
	 */
	public function isConnected() {
		return $this->connected;
	}

	/**
	 * Connection
	 */
	public function getConnection() {
	   return $this->connection;
	}

	/**
	 * Set if is owner
	 * @param $owner
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}

	/**
	 * Is owner
	 * @return bool
	 */
	public function isOwner() {
		return $this->owner;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->_user ? $this->_user->user->username : '';
	}

	/**
	 * Set index
	 * @param $index
	 */
	public function setIndex($index) {
		$this->index = $index;
	}

	/**
	 * Get opponent
	 * @return mixed
	 */
	public function getOpponent() {
		return $this->game->getOpponent($this->index);
	}

	/**
	 * Send
	 * @param $data
	 */
	public function send($data) {
		if ($this->isConnected()) {
			$event = new UserSendEvent();
			$event->player = $this;
			$this->game->trigger('user-message', $event);

			$this->connection->send(json_encode($data));
		}
	}

	/**
	 * Set if is new
	 * @param $new
	 */
	public function setIsNew($new) {
		$this->new = $new;
	}

	/**
	 * Is new
	 * @return bool
	 */
	public function isNew() {
		return $this->new;
	}

	/**
	 * Set if user is active
	 * @param $active
	 */
	public function setActive($active) {
		$this->active = $active;
	}

	/**
	 * Check if user is active now
	 * @return bool
	 */
	public function isActive() {
		return $this->active;
	}
}