<?php
namespace console\base;

use common\models\DeckCard;

/**
 * Class CardFactory
 * @package console\base
 */
class CardFactory {

	/**
	 * List of cards
	 * @var array
	 */
	protected $ids = [];

	/**
	 * Current card offset
	 * @var int
	 */
	protected $card = 0;

	/**
	 * Loaded cards
	 * @var array
	 */
	private static $cards = [];

	/**
	 * Set cards Ids
	 * @param $ids
	 */
	public function setCards($ids) {
		$this->ids = is_string($ids) ? explode(',', $ids) : $ids;
	}

	/**
	 * Set IDs
	 * @param $ids
	 */
	public function setIds($ids) {
		$this->ids = is_string($ids) ? explode(',', $ids) : $ids;
	}

	/**
	 * Get cards
	 * @param int $number
	 * @return array
	 */
	public function get($number = 1) {
		$data = [];
		for ($i=1; $i<=$number; $i++) {
			$this->card++;
			$data[] = self::load($this->ids[$this->card - 1]);
		}

		return $data;
	}

	/**
	 * Load cards by id
	 * @param int $id
	 */
	public static function load($id) {
		if (!isset(self::$cards[$id])) {

			$card = DeckCard::findOne($id);
			$item = $card->getAttributes([
				'id',
				'title',
				'description',
				'cost',
				'type'
			]);
			$item['img'] = $card->img->getThumb('60x80');

			$c = self::getCard($card);
			$c->setAttributes($item);
			unset($card, $item);

			self::$cards[$id] = $c;
		}

		return self::$cards[$id];
	}

	/**
	 * Get card
	 * @param $c
	 * @return BoostCard|DamageCard|UnitCard
	 */
	protected static function getCard($c) {
		switch ($c->type) {
			case 1:
				$card = new UnitCard();
				break;
			case 2:
				$card = new DamageCard();
				break;
			default:
				$card = new BoostCard();
				break;
		}

		return $card;
	}
}