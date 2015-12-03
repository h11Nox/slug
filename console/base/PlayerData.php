<?php
namespace console\base;

/**
 * PlayerData class
 */
class PlayerData extends \common\base\PlayerData{

	protected $cards = [];
	protected $hand = [];

	/**
	 * Add cards to user
	 * @param array $cards
	 */
	public function addCards(array $cards) {
		foreach ($cards as $card) {
			$this->cards[] = $card;
		}
	}

	/**
	 * Get card by index
	 * @param $index
	 * @return Card
	 */
	public function getCard($index) {
		return $this->cards[$index];
	}

	/**
	 * Remove card by index
	 * @param $index
	 */
	public function useCard($index) {
		if (isset($this->cards[$index])) {
			$this->usePoints($this->cards[$index]->cost);
			$this->addToHand($this->cards[$index]);
			unset($this->cards[$index]);
			// Reorder all card keys
			$this->cards = array_values($this->cards);
		}
	}

	/**
	 * Get user response
	 * @return array
	 */
	public function getResponse(){
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
	protected function addToHand($card) {
		$this->hand[] = [
			'card' => $card,
			'data' => []
		];
	}

	/**
	 * Prepare card for response
	 * @param $card
	 * @param $key
	 */
	protected function prepareCard(&$card, $key) {
		$card['id'] = $key;
	}
}