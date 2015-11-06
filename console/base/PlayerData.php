<?php
namespace console\base;

/**
 * PlayerData class
 */
class PlayerData extends \common\base\PlayerData{

	protected $cards = [];
	protected $newCards = [];

	/**
	 * Add cards to user
	 * @param array $cards
	 */
	public function addCards(array $cards) {
		foreach ($cards as $card) {
			$this->newCards[] = $card;
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
			unset($this->cards[$index]);
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
			'newCards' => []
		];

		foreach ($this->cards as $card) {
			$response['cards'][] = $card->getAttributes();
		}
		$index = count($this->cards);
		foreach ($this->newCards as $card){
			$response['newCards'][] = array_merge($card->getAttributes(), [
				'id' => $index
			]);
			$this->cards[$index] = $card;
			$index++;
		}
		$this->newCards = [];

		return $response;
	}
}