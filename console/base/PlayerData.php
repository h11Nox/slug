<?php
namespace console\base;
use console\base\interfaces\Damageable;

/**
 * PlayerData class
 */
class PlayerData extends \common\base\PlayerData
{

	protected $cards = [];
	protected $hand = [];

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
	public function getCard($index)
	{
		if (!isset($this->cards[$index])) {
			throw new \Exception("Undefined card with index {$index}");
		}
		return $this->cards[$index];
	}

	/**
	 * Remove card by index
	 * @param $index
	 * @param null $type
	 */
	public function useCard($index, $type = null)
	{
		if (isset($this->cards[$index])) {
			$card = $this->cards[$index];
			$this->usePoints($card->cost);
			if ($type === 'unit') {
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
		array_map(function($handCard) use ($afterTurn) {
			$afterTurn($handCard['card']);
		}, $this->hand);
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
			'maxMp' => $this->getMaxPoint(),
			'cards' => [],
			'hand' => []
		];

		foreach ($this->cards as $card) {
			$response['cards'][] = $card->getAttributes();
		}
		array_walk($response['cards'], [$this, 'prepareCard']);
		foreach ($this->hand as $card) {
			$response['hand'][] = [
				'card' => $card['card']->getAttributes(),
				'data' => $card['data'],
			];
		}

		return $response;
	}

	/**
	 * @param $card
	 */
	protected function addToHand($card)
	{
		$this->hand[] = [
			'card' => $card,
			'data' => $card->getParams(),
		];
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