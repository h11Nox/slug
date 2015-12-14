<?php
namespace console\base;

use common\models\DeckCard;

/**
 * PlayerData class
 */
class PlayerData extends \common\base\PlayerData {

	protected $cards = [];
	protected $hand = [];
	protected $timer;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->timer = new PlayerTimer();
	}

	/**
	 * Get player time
	 * @return mixed
	 */
	public function getTime() {
		try {
			$time = $this->timer->getTime();
		} catch (TimerException $e) {
			$time = -1;
		}
		return $time;
	}

	/**
	 * Get timer
	 * @return PlayerTimer
	 */
	public function getTimer() {
		return $this->timer;
	}

	/**
	 * Add cards to user
	 * @param array $cards
	 */
	public function addCards(array $cards)
	{
		foreach ($cards as $card) {
			$this->cards[] = $card;
		}
	}

	/**
	 * Get card by index
	 * @param $index
	 * @return Card
	 * @throws \Exception
	 */
	public function getCard($index) {
		if (!isset($this->cards[$index])) {
			throw new \Exception("Undefined card with index {$index}");
		}
		return $this->cards[$index];
	}

	/**
	 * Get card from hand by index
	 * @param $index
	 * @return mixed
	 * @throws \Exception
	 */
	public function getHandCard($index) {
		if (!isset($this->hand[$index])) {
			throw new \Exception("Undefined card in hand with index {$index}");
		}
		return $this->hand[$index];
	}

	/**
	 * Remove card by index
	 * @param $index
	 */
	public function useCard($index)
	{
		if (isset($this->cards[$index])) {
			$card = $this->cards[$index];
			$this->usePoints($card->cost);
			if ($card->type == DeckCard::TYPE_WARRIOR) {
				$this->addToHand($this->cards[$index]);
			}

			unset($this->cards[$index]);
			// Reorder all card keys
			$this->cards = array_values($this->cards);
		}
	}

	/**
	 * Do something after turn
	 */
	public function afterTurn() {
		$afterTurn = function($c) {
			$c->afterTurn();
		};
		array_map($afterTurn, $this->cards);
		array_map($afterTurn, $this->hand);
	}

	/**
	 * Get user response
	 * @return array
	 */
	public function getResponse()
	{
		$response = [
			'health' => $this->getHealth(),
			'mp' => $this->getPoints(),
			'maxMp' => max($this->getPoints(), $this->getMaxPoint()),
			'cards' => [],
			'hand' => [],
			'time' => $this->getTime()
		];

		foreach ($this->cards as $card) {
			$response['cards'][] = $card->getAttributes();
		}
		array_walk($response['cards'], [$this, 'prepareCard']);
		foreach ($this->hand as $card) {
			$response['hand'][] = [
				'card' => $card->getAttributes(),
				'data' => $card->getParams(),
			];
		}

		return $response;
	}

	/**
	 * @param $card
	 */
	protected function addToHand($card)
	{
		$card->on(UnitCard::DEATH_EVENT, [$this, 'removeFromHand']);
		$card->setIndex(count($this->hand));
		$this->hand[] = $card;
	}

	/**
	 * Remove card from hand
	 * @param $event
	 * @throws \Exception
	 * @internal param $index
	 */
	public function removeFromHand($event) {
		$this->getHandCard($event->index);
		unset($this->hand[$event->index]);
	}

	/**
	 * Prepare card for response
	 * @param $card
	 * @param $key
	 */
	protected function prepareCard(&$card, $key)
	{
		$card['id'] = $key;
	}

	/**
	 * Receive some damage
	 *
	 * @param $damage
	 */
	public function receiveDamage($damage) {
		$this->hp -= $damage;
		if ($this->hp <= 0) {
			// end game
		}
	}
}