<?php
namespace console\base;

use console\base\events\UseCardEvent;
use console\base\events\UserSendEvent;
use console\base\http\Response;
use console\base\interfaces\Boostable;
use console\base\interfaces\Damageable;
use console\base\interfaces\UnitInterface;
use Ratchet\ConnectionInterface;

/**
 * Player Class
 */
class Player extends \common\base\Player implements UnitInterface {

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
	public function getResponse() {
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
	 * @param array $data
	 * @throws InvalidUnitTypeException
	 */
	public function useCard($cardID, $data = []) {
		if (!is_object($data)) {
			$data = (object)$data;
		}
		$card = $this->data->getCard($cardID);
		$card->setIndex($cardID);
		if ($card->cost <= $this->data->getPoints()) {
			// Use mp
			if ($card instanceof Damageable) {
				if (!in_array($data->type, ['player', 'unit'])) {
					throw new InvalidUnitTypeException();
				}
				$unit = $this->getOpponent();
				if ($data->type === 'unit') {
					$unit = $unit->getData()->getHandCard($data->index);
				}
				$card->damage($unit, $card->damage);
			} elseif ($card instanceof Boostable) {
				$card->boost($this);
			}
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
	 * Get opponent
	 * @return mixed
	 */
	public function getOpponent() {
		return $this->game->getOpponent($this->index);
	}

	/**
	 * Send
	 * @param $response
	 */
	public function send(Response $response) {
		if ($this->isConnected()) {
			$event = new UserSendEvent();
			$event->player = $this;
			$this->game->trigger('user-message', $event);

			$this->connection->send(json_encode($response->getResponseData()));
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

	/**
	 * Provides possibility to damaged
	 *
	 * @param $damage
	 * @return void
	 */
	public function receiveDamage($damage) {
		$this->getData()->receiveDamage($damage);
	}
}

class InvalidUnitTypeException extends \Exception{}