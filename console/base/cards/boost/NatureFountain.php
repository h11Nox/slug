<?php
namespace console\base\cards\boost;
use console\base\interfaces\UnitInterface;
use console\base\Player;

/**
 * Class NatureFountain
 * @package console\base\cards\boost
 */
class NatureFountain extends BoostBase {

	/**
	 * Boost
	 * @param UnitInterface $unit
	 * @return void
	 * @throws BoostUnitException
	 */
	public function boostUnit(UnitInterface $unit) {
		if (!($unit instanceof Player)) {
			throw new BoostUnitException('Boost card can be applied only to player');
		}
		$unit->getData()->addPoints($this->mp);
	}
}