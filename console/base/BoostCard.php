<?php
namespace console\base;

use console\base\cards\boost\BoostBase;
use console\base\interfaces\Boostable;
use console\base\interfaces\UnitInterface;

/**
 * Class BoostCard
 * @package console\base
 */
class BoostCard extends Card implements Boostable {

	/**
	 * Boost card worker
	 * @var BoostBase
	 */
	private $boostCard;

	/**
	 * Card params
	 * @var array
	 */
	protected $params = [ 'class', 'mp' ];

	/**
	 * Boots unit
	 * @param UnitInterface $unit
	 */
	public function boost(UnitInterface $unit) {
		$this->getCard()->boostUnit($unit);
	}

	/**
	 * Get card
	 * @return BoostBase
	 * @throws \InvalidArgumentException
	 */
	private function getCard() {
		if (is_null($this->boostCard)) {
			if (empty($this->class)) {
				throw new \InvalidArgumentException('Boost card class not defined...');
			}

			$className = 'console\base\cards\boost\\'.$this->class;
			$this->boostCard = new $className;
			$this->boostCard->mp = $this->mp;
		}
		return $this->boostCard;
	}
}